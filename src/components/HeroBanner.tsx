import React from 'react';
import Link from 'next/link';
import { Clock, ArrowUpRight, Flame } from 'lucide-react';
import { Article } from '../types/news';
import { HomeBannerData } from '../lib/newsApi';

interface HeroBannerProps {
  leadArticle: Article;
  sideArticles: Article[];
  customBanner?: HomeBannerData | null;
}

export default function HeroBanner({ leadArticle, sideArticles, customBanner }: HeroBannerProps) {
  const displayTitle = customBanner?.title || leadArticle.title;
  const displaySubtitle = customBanner?.subtitle || leadArticle.subtitle;
  const displayDesc = customBanner?.description || leadArticle.excerpt;
  const displayAuthor = customBanner?.author?.name || leadArticle.author.name;
  const displayRole = customBanner?.author?.role || leadArticle.author.role;
  const displayImage = customBanner?.bannerImage || leadArticle.imageUrl;
  const displayBadge = customBanner?.badge || leadArticle.category;
  const displayLink = (customBanner?.linkUrl && customBanner.linkUrl !== '#') 
    ? customBanner.linkUrl 
    : `/article/${leadArticle.id}`;

  return (
    <section className="mb-10">
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 border-b border-stone-200 dark:border-stone-800 pb-10">
        {/* Main Lead Story (8 cols) */}
        <div className="lg:col-span-8 group">
          <div className="relative aspect-[16/9] w-full overflow-hidden rounded-xl bg-stone-100 dark:bg-stone-800 mb-4 shadow-sm">
            {/* Category and breaking tag */}
            <div className="absolute top-3 left-3 z-10 flex items-center gap-2">
              <span className="px-2.5 py-1 text-xs font-bold bg-red-600 text-white rounded">
                {displayBadge}
              </span>
              {leadArticle.isBreaking && (
                <span className="px-2.5 py-1 text-xs font-bold bg-stone-900/90 text-amber-400 rounded backdrop-blur-sm flex items-center gap-1">
                  <Flame className="w-3.5 h-3.5 text-amber-400" />
                  പ്രധാന വാർത്ത
                </span>
              )}
            </div>

            <img
              src={displayImage}
              alt={displayTitle}
              className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
          </div>

          <div>
            <div className="flex items-center gap-3 text-xs text-stone-500 dark:text-stone-400 mb-2">
              <span className="font-semibold text-stone-800 dark:text-stone-200">
                റിപ്പോർട്ട്: {displayAuthor} {displayRole ? `(${displayRole})` : ''}
              </span>
              <span>•</span>
              <span className="flex items-center gap-1">
                <Clock className="w-3 h-3" />
                {leadArticle.readTime}
              </span>
              <span>•</span>
              <span>{leadArticle.publishedAt}</span>
            </div>

            <Link href={displayLink} className="block group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors">
              <h2 className="font-serif text-2xl sm:text-3xl md:text-4xl font-bold leading-snug text-stone-900 dark:text-stone-50 mb-2">
                {displayTitle}
              </h2>
            </Link>

            {displaySubtitle && (
              <p className="text-sm font-semibold text-stone-700 dark:text-stone-300 mb-2">
                {displaySubtitle}
              </p>
            )}

            <p className="text-stone-600 dark:text-stone-300 text-sm sm:text-base leading-relaxed line-clamp-3 mb-4">
              {displayDesc}
            </p>

            <div className="flex items-center justify-between pt-2">
              <Link
                href={displayLink}
                className="inline-flex items-center gap-1.5 text-xs font-bold text-red-700 dark:text-red-400 hover:text-red-800 group-hover:translate-x-0.5 transition"
              >
                മുഴുവൻ വാർത്ത വായിക്കുക
                <ArrowUpRight className="w-4 h-4" />
              </Link>

              <div className="flex items-center gap-2">
                {leadArticle.tags.slice(0, 3).map((tag) => (
                  <span
                    key={tag}
                    className="text-[11px] font-medium text-stone-500 dark:text-stone-400 bg-stone-100 dark:bg-stone-800 px-2 py-0.5 rounded"
                  >
                    #{tag}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* Side Stories (4 cols) */}
        <div className="lg:col-span-4 flex flex-col justify-between space-y-6 lg:border-l lg:border-stone-200 lg:dark:border-stone-800 lg:pl-8">
          <div className="border-b border-stone-200 dark:border-stone-800 pb-2">
            <h3 className="font-serif text-lg font-bold text-stone-900 dark:text-stone-100 flex items-center justify-between">
              <span>ഇന്നത്തെ തലക്കെട്ടുകൾ</span>
              <span className="text-xs font-sans font-semibold text-red-600">EDITION 1</span>
            </h3>
          </div>

          <div className="space-y-6 divide-y divide-stone-200 dark:divide-stone-800">
            {sideArticles.map((art, idx) => (
              <div key={art.id} className={idx > 0 ? 'pt-6 group' : 'group'}>
                <div className="flex gap-4">
                  <div className="flex-1">
                    <span className="text-[10px] font-bold text-red-700 dark:text-red-400 mb-1 inline-block">
                      {art.category}
                    </span>
                    <Link
                      href={`/article/${art.id}`}
                      className="block font-serif text-sm sm:text-base font-bold text-stone-900 dark:text-stone-100 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors leading-snug mb-2"
                    >
                      {art.title}
                    </Link>
                    <p className="text-xs text-stone-600 dark:text-stone-400 line-clamp-2 mb-2">
                      {art.excerpt}
                    </p>
                    <div className="flex items-center gap-2 text-[11px] text-stone-400">
                      <span>{art.readTime}</span>
                    </div>
                  </div>

                  <div className="w-24 h-24 sm:w-28 sm:h-24 flex-shrink-0 relative rounded-lg overflow-hidden bg-stone-100 dark:bg-stone-800">
                    <img
                      src={art.imageUrl}
                      alt={art.title}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                  </div>
                </div>
              </div>
            ))}
          </div>

          </div>
      </div>
    </section>
  );
}
