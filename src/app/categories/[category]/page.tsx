import React from 'react';
import { notFound } from 'next/navigation';
import { ARTICLES, CATEGORIES_LIST } from '../../../data/mockData';
import CategoryClientView from './CategoryClientView';
import { getArticles, getCategories, isNewsApiConfigured } from '../../../lib/newsApi';

interface CategoryPageProps {
  params: {
    category: string;
  };
}

export const dynamic = 'force-dynamic';

export async function generateMetadata({ params }: CategoryPageProps) {
  const decoded = decodeURIComponent(params.category).toLowerCase();
  const categories = isNewsApiConfigured() ? await getCategories() : CATEGORIES_LIST;
  const matched = categories.find(
    (c) => c.id === decoded || c.nameMalayalam.toLowerCase() === decoded
  );

  const title = matched ? matched.nameMalayalam : 'വാർത്തകൾ';

  return {
    title: `${title} | എൻ-ന്യൂസ് News Portal (N-NEWS)`,
    description: matched?.description || 'ഏറ്റവും പുതിയ വാർത്തകളും വിശകലനങ്ങളും.',
  };
}

export default async function CategoryPage({ params }: CategoryPageProps) {
  const decoded = decodeURIComponent(params.category).toLowerCase();

  const categories = isNewsApiConfigured() ? await getCategories() : CATEGORIES_LIST;
  const matched = categories.find(
    (c) => c.id === decoded || c.nameMalayalam.toLowerCase() === decoded
  );

  if (!matched && decoded !== 'all') {
    notFound();
  }

  const categoryTitle = matched ? matched.nameMalayalam : 'എല്ലാ വാർത്തകളും';
  const categoryDesc = matched?.description;

  const articles = isNewsApiConfigured()
    ? await getArticles(decoded === 'all' || !matched ? '' : `?category=${encodeURIComponent(matched.id)}`)
    : decoded === 'all' || !matched
      ? ARTICLES
      : ARTICLES.filter(
          (a) => a.categorySlug === matched.id || a.category === matched.nameMalayalam
        );

  return (
    <CategoryClientView
      categoryTitle={categoryTitle}
      categoryDescription={categoryDesc}
      articles={articles}
    />
  );
}
