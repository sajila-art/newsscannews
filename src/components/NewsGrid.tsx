'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Clock, ArrowUpRight, BookOpen } from 'lucide-react';
import { Article, CategoryItem } from '../types/news';
import { CATEGORIES_LIST } from '../data/mockData';

interface NewsGridProps {
  articles: Article[];
  categories?: CategoryItem[];
  maxItems?: number;
}

export default function NewsGrid({ articles, categories = CATEGORIES_LIST, maxItems = 9 }: NewsGridProps) {
  const [activeSlug, setActiveSlug] = useState('all');

  const filteredArticles = (activeSlug === 'all'
    ? articles
    : articles.filter((a) => {
        const matchedCat = categories.find((c) => c.id === activeSlug || c.slug === activeSlug);
        return (
          a.categorySlug === activeSlug ||
          a.category === activeSlug ||
          (matchedCat && (
            a.category === matchedCat.nameMalayalam ||
            a.category === matchedCat.nameEnglish ||
            a.categorySlug === matchedCat.slug ||
            a.categorySlug === matchedCat.id
          ))
        );
      }))
    .slice()
    .sort((a, b) => new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime());

  const displayedArticles = filteredArticles.slice(0, maxItems);

  return (
    <section className="mb-12">
      {/* Section Header with Category Tabs */}
      <div className="border-b-2 border-stone-900 dark:border-stone-100 pb-3 mb-6">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
          <h3 className="font-serif text-2xl font-black text-stone-900 dark:text-stone-100 flex items-center gap-2">
            <span>പ്രധാന പംക്തികൾ</span>
            <span className="text-xs font-sans font-normal text-stone-500">
              ({displayedArticles.length} വാർത്തകൾ)
            </span>
          </h3>
        </div>

        {/* Categories Bar */}
        <div className="flex items-center gap-1.5 overflow-x-auto pb-2 scrollbar-none">
          <button
            onClick={() => setActiveSlug('all')}
            className={`px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap transition-colors ${
              activeSlug === 'all'
                ? 'bg-red-700 text-white'
                : 'bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 hover:bg-stone-200 dark:hover:bg-stone-700'
            }`}
          >
            എല്ലാം (All)
          </button>
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setActiveSlug(cat.id)}
              className={`px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap transition-colors ${
                activeSlug === cat.id
                  ? 'bg-red-700 text-white'
                  : 'bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 hover:bg-stone-200 dark:hover:bg-stone-700'
              }`}
            >
              {cat.nameMalayalam}
            </button>
          ))}
        </div>
      </div>

      {/* Grid Layout */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {displayedArticles.map((article) => (
          <article
            key={article.id}
            className="flex flex-col justify-between bg-white dark:bg-stone-900/60 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden hover:shadow-lg transition group"
          >
            <div>
              {/* Image */}
              <div className="relative aspect-[16/10] w-full overflow-hidden bg-stone-100 dark:bg-stone-800">
                <img
                  src={article.imageUrl}
                  alt={article.title}
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
              </div>

              {/* Body */}
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
                  <h4 className="font-serif text-base sm:text-lg font-bold text-stone-900 dark:text-stone-100 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors leading-snug mb-2 line-clamp-2">
                    {article.title}
                  </h4>
                </Link>

                <p className="text-xs text-stone-600 dark:text-stone-300 leading-relaxed line-clamp-3 mb-4">
                  {article.excerpt}
                </p>
              </div>
            </div>

            {/* Bottom Footer info */}
            <div className="px-5 pb-4 pt-2 border-t border-stone-100 dark:border-stone-800/80 flex items-center justify-between">
              <span className="text-[11px] text-stone-400">{article.publishedAt.split(' ')[0]}</span>

              <div className="flex items-center gap-3">
                {article.scannedPageRef && (
                  <Link
                    href={`/scan-news?page=${article.scannedPageRef.pageNumber}`}
                    className="text-[11px] font-semibold text-red-700 dark:text-red-400 hover:underline flex items-center gap-1"
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
    </section>
  );
}
