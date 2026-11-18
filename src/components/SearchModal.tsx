'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { Search, X, ArrowRight, FolderOpen, Loader2 } from 'lucide-react';
import { Article, CategoryItem } from '../types/news';

interface SearchModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function SearchModal({ isOpen, onClose }: SearchModalProps) {
  const router = useRouter();
  const [query, setQuery] = useState('');
  const [categories, setCategories] = useState<CategoryItem[]>([]);
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(false);
  const [apiError, setApiError] = useState('');

  // Close on Escape key
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        onClose();
      }
    };
    if (isOpen) {
      window.addEventListener('keydown', handleKeyDown);
    }
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, onClose]);

  // Fetch categories & articles from database as user searches
  useEffect(() => {
    if (!isOpen) return;

    let cancelled = false;
    const timer = setTimeout(async () => {
      setLoading(true);
      setApiError('');
      try {
        const res = await fetch(`/api/search?q=${encodeURIComponent(query.trim())}`, {
          cache: 'no-store',
        });
        const data = await res.json().catch(() => null);
        if (cancelled) return;

        if (!res.ok) {
          setApiError(data?.error || 'ഡാറ്റാബേസിൽ തിരയാൻ കഴിഞ്ഞില്ല.');
          setCategories([]);
          setArticles([]);
          return;
        }

        setCategories(Array.isArray(data?.categories) ? data.categories : []);
        setArticles(Array.isArray(data?.articles) ? data.articles : []);
      } catch {
        if (!cancelled) {
          setApiError('സെർവറുമായി ബന്ധപ്പെടാൻ കഴിഞ്ഞില്ല.');
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    }, query.trim() ? 200 : 0);

    return () => {
      cancelled = true;
      clearTimeout(timer);
    };
  }, [isOpen, query]);

  if (!isOpen) return null;

  const trimmed = query.trim();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (categories.length > 0) {
      onClose();
      router.push(`/categories/${encodeURIComponent(categories[0].id)}`);
      return;
    }
    if (articles.length > 0 && articles[0].categorySlug) {
      onClose();
      router.push(`/categories/${encodeURIComponent(articles[0].categorySlug)}`);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 p-4 bg-stone-950/70 backdrop-blur-sm">
      <div className="bg-white dark:bg-stone-900 w-full max-w-2xl rounded-xl shadow-2xl border border-stone-300 dark:border-stone-700 overflow-hidden flex flex-col max-h-[80vh]">
        {/* Search Input Bar */}
        <form onSubmit={handleSubmit} className="p-4 border-b border-stone-200 dark:border-stone-800 flex items-center gap-3">
          {loading ? (
            <Loader2 className="w-5 h-5 text-red-600 animate-spin flex-shrink-0" />
          ) : (
            <Search className="w-5 h-5 text-stone-400 flex-shrink-0" />
          )}
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="വാർത്തകൾ, വിഭാഗങ്ങൾ തിരയുക (Enter അമർത്തിയാൽ വിഭാഗത്തിലേക്ക് പോകും)..."
            autoFocus
            className="flex-1 bg-transparent text-sm sm:text-base text-stone-900 dark:text-stone-100 placeholder-stone-400 focus:outline-none"
          />
          {query && (
            <button
              type="button"
              onClick={() => setQuery('')}
              className="p-1 text-stone-400 hover:text-stone-600 dark:hover:text-stone-200"
            >
              <X className="w-4 h-4" />
            </button>
          )}
          <button
            type="button"
            onClick={onClose}
            className="text-xs px-2 py-1 rounded bg-stone-100 dark:bg-stone-800 text-stone-500 hover:text-stone-800 dark:hover:text-stone-200"
          >
            ESC
          </button>
        </form>

        {/* Quick Category Shortcuts from DB */}
        {!trimmed && categories.length > 0 && (
          <div className="p-4 bg-stone-50 dark:bg-stone-950 border-b border-stone-200 dark:border-stone-800">
            <span className="text-[11px] font-bold text-stone-400 block mb-2">
              പ്രധാന വിഭാഗങ്ങൾ:
            </span>
            <div className="flex flex-wrap gap-2">
              {categories.slice(0, 8).map((cat) => (
                <Link
                  key={cat.id}
                  href={`/categories/${encodeURIComponent(cat.id)}`}
                  onClick={onClose}
                  className="px-2.5 py-1 text-xs rounded-full bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-300 hover:bg-red-50 hover:text-red-600 transition"
                >
                  {cat.nameMalayalam}
                </Link>
              ))}
            </div>
          </div>
        )}

        {/* Results Area */}
        <div className="flex-1 overflow-y-auto p-4 space-y-4">
          {trimmed && !loading && categories.length === 0 && articles.length === 0 && (
            <div className="text-center py-8 text-stone-400 text-sm">
              {apiError || (
                <>
                  &quot;{query}&quot; എന്നതുമായി ബന്ധപ്പെട്ട വിഭാഗങ്ങളോ വാർത്തകളോ കണ്ടെത്താനായില്ല.
                </>
              )}
            </div>
          )}

          {/* Matching Categories (Redirect to Category Detail Page) */}
          {trimmed && categories.length > 0 && (
            <div>
              <div className="flex items-center gap-1.5 text-xs font-bold text-red-600 mb-2">
                <FolderOpen className="w-3.5 h-3.5" />
                <span>വിഭാഗങ്ങൾ ({categories.length})</span>
              </div>
              <div className="space-y-2">
                {categories.map((cat) => (
                  <Link
                    key={cat.id}
                    href={`/categories/${encodeURIComponent(cat.id)}`}
                    onClick={onClose}
                    className="flex items-center justify-between p-3 rounded-lg bg-stone-50 dark:bg-stone-800/60 hover:bg-red-50 dark:hover:bg-red-950/30 border border-stone-200 dark:border-stone-700 transition"
                  >
                    <div>
                      <p className="text-xs font-bold text-stone-900 dark:text-stone-100">
                        {cat.nameMalayalam} {cat.nameEnglish ? `(${cat.nameEnglish})` : ''}
                      </p>
                      {cat.description && (
                        <p className="text-[11px] text-stone-500 line-clamp-1">
                          {cat.description}
                        </p>
                      )}
                    </div>
                    <ArrowRight className="w-4 h-4 text-red-600 flex-shrink-0" />
                  </Link>
                ))}
              </div>
            </div>
          )}

          {/* Matching Articles from Database */}
          {articles.length > 0 && (
            <div>
              <div className="text-xs font-bold text-stone-400 mb-2">
                {trimmed ? `വാർത്തകൾ (${articles.length})` : `ഏറ്റവും പുതിയ വാർത്തകൾ (${articles.length})`}
              </div>
              <div className="space-y-3">
                {articles.map((art) => (
                  <div
                    key={art.id}
                    className="p-3 rounded-lg hover:bg-stone-100 dark:hover:bg-stone-800 transition border border-transparent hover:border-stone-200 dark:hover:border-stone-700"
                  >
                    <div className="flex items-center justify-between gap-2 text-[10px] font-bold text-red-600 mb-1">
                      <Link
                        href={`/categories/${encodeURIComponent(art.categorySlug || 'all')}`}
                        onClick={onClose}
                        className="hover:underline"
                      >
                        {art.category || 'വാർത്തകൾ'} →
                      </Link>
                      <span className="text-stone-400 font-normal">{art.publishedAt}</span>
                    </div>
                    <Link
                      href={`/categories/${encodeURIComponent(art.categorySlug || 'all')}`}
                      onClick={onClose}
                      className="block"
                    >
                      <h5 className="font-serif text-sm font-bold text-stone-900 dark:text-stone-100 line-clamp-1">
                        {art.title}
                      </h5>
                      <p className="text-xs text-stone-500 dark:text-stone-400 line-clamp-2 mt-0.5">
                        {art.excerpt}
                      </p>
                    </Link>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
