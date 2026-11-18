import React from 'react';
import Ticker from '../components/Ticker';
import HeroBanner from '../components/HeroBanner';
import NewsGrid from '../components/NewsGrid';
import SidebarTrending from '../components/SidebarTrending';
import { ARTICLES, CATEGORIES_LIST } from '../data/mockData';
import { getArticles, getCategories, getHomeBanner, getTodaysAudio } from '../lib/newsApi';

export const dynamic = 'force-dynamic';

export default async function HomePage() {
  const fetchedArticles = await getArticles();
  const articles = fetchedArticles && fetchedArticles.length > 0 ? fetchedArticles : ARTICLES;
  const categories = await getCategories({ homeOnly: true });
  const homeBanner = await getHomeBanner();
  const todaysAudio = await getTodaysAudio();

  const leadArticle = articles.find((a) => a.isFeatured) || articles[0];
  const sideArticles = articles.filter((a) => a.id !== leadArticle?.id).slice(0, 3);
  const remainingArticles = articles.filter((a) => a.id !== leadArticle?.id);

  return (
    <main>
      {/* Live Breaking News Ticker */}
      <Ticker />

      {/* Main Content Area */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
        {/* Lead Headline + Front Page Digest */}
        {leadArticle && (
          <HeroBanner 
            leadArticle={leadArticle} 
            sideArticles={sideArticles} 
            customBanner={homeBanner}
          />
        )}

        {/* Two-Column Editorial & Trending Section */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-4">
          <div className="lg:col-span-8">
            <NewsGrid articles={remainingArticles} categories={categories} />
          </div>
          <div className="lg:col-span-4">
            <SidebarTrending articles={articles} todaysAudio={todaysAudio} />
          </div>
        </div>
      </div>
    </main>
  );
}
