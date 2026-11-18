'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Clock, BookOpen, ArrowUpRight, ChevronLeft } from 'lucide-react';
import { Article } from '../../../types/news';

interface CategoryClientViewProps {
  categoryTitle: string;
  categoryDescription?: string;
  articles: Article[];
}

export default function CategoryClientView({
  categoryTitle,
  categoryDescription,
  articles,
}: CategoryClientViewProps) {
  const [sortBy, setSortBy] = useState<'latest' | 'popular'>('latest');

  const displayed = [...articles];

  if (sortBy === 'popular') {
    displayed.sort((a, b) => (b.views || 0) - (a.views || 0));
  }

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      {/* Breadcrumb */}
      <div className="mb-4">
        <Link
          href="/"
          className="text-xs text-stone-500 hover:text-red-600 inline-flex items-center gap-1 font-medium"
        >
          <ChevronLeft className="w-3.5 h-3.5" />
          പ്രധാന പേജിലേക്ക് മടങ്ങുക (Home)
        </Link>
      </div>

      {/* Header Banner */}
      <div className="border-b-2 border-stone-900 dark:border-stone-100 pb-4 mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <span className="text-xs font-bold uppercase tracking-wider text-red-600">
            വിഭാഗം (Category)
          </span>
          <h1 className="font-serif text-3xl sm:text-5xl font-black text-stone-900 dark:text-stone-100 tracking-tight mt-1">
            {categoryTitle}
          </h1>
          {categoryDescription && (
            <p className="text-xs sm:text-sm text-stone-500 dark:text-stone-400 mt-2">
              {categoryDescription}
            </p>
          )}
        </div>

        {/* Filter Controls */}
        <div className="flex flex-wrap items-center gap-3">
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value as 'latest' | 'popular')}
            className="bg-stone-100 dark:bg-stone-800 text-stone-800 dark:text-stone-200 text-xs font-semibold rounded-lg px-3 py-1.5 border border-stone-300 dark:border-stone-700 focus:outline-none"
          >
            <option value="latest">പുതിയ വാർത്തകൾ ആദ്യം</option>
            <option value="popular">കൂടുതൽ വായിക്കപ്പെട്ടവ</option>
          </select>
        </div>
      </div>

      {/* Article Grid */}
      {displayed.length === 0 ? (
        <div className="text-center py-16 text-stone-500 text-sm">
          ഈ വിഭാഗത്തിൽ നിലവിൽ വാർത്തകൾ ലഭ്യമല്ല.
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {displayed.map((article) => (
            <article
              key={article.id}
              className="flex flex-col justify-between bg-white dark:bg-stone-900/60 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden hover:shadow-lg transition group"
            >
              <div>
                <div className="relative aspect-[16/10] w-full overflow-hidden bg-stone-100 dark:bg-stone-800">
                  <span className="absolute top-2.5 left-2.5 z-10 px-2.5 py-0.5 text-[11px] font-bold bg-red-600 text-white rounded">
                    {article.category}
                  </span>
                  <img
                    src={article.imageUrl}
                    alt={article.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  />
                </div>

                <div className="p-5">
                  <div className="flex items-center gap-2 text-xs text-stone-500 dark:text-stone-400 mb-2 font-medium">
                    <span>{article.author.name}</span>
                    <span>•</span>
                    <span className="flex items-center gap-1">
                      <Clock className="w-3 h-3" />
                      {article.readTime}
                    </span>
                  </div>

                  <Link href={`/article/${article.id}`}>
                    <h3 className="font-serif text-base sm:text-lg font-bold text-stone-900 dark:text-stone-100 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors leading-snug mb-2 line-clamp-2">
                      {article.title}
                    </h3>
                  </Link>

                  <p className="text-xs text-stone-600 dark:text-stone-300 leading-relaxed line-clamp-3 mb-4">
                    {article.excerpt}
                  </p>
                </div>
              </div>

              <div className="px-5 pb-4 pt-2 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                <span className="text-[11px] text-stone-400">
                  {article.publishedAt.split(' ')[0]}
                </span>

                <div className="flex items-center gap-3">
                  {article.scannedPageRef && (
                    <Link
                      href={`/scan-news?page=${article.scannedPageRef.pageNumber}`}
                      className="text-[11px] font-semibold text-red-600 dark:text-red-400 hover:underline flex items-center gap-1"
                    >
                      <BookOpen className="w-3 h-3" />
                      പേജ് {article.scannedPageRef.pageNumber} സ്കാൻ
                    </Link>
                  )}
                  <Link
                    href={`/article/${article.id}`}
                    className="text-xs font-bold text-stone-800 dark:text-stone-200 hover:text-red-600 flex items-center gap-0.5"
                  >
                    വായിക്കുക <ArrowUpRight className="w-3.5 h-3.5" />
                  </Link>
                </div>
              </div>
            </article>
          ))}
        </div>
      )}
    </div>
  );
}
