import React from 'react';
import { notFound } from 'next/navigation';
import { ARTICLES } from '../../../data/mockData';
import ArticleClientView from './ArticleClientView';
import { getArticle, getArticles, isNewsApiConfigured, NewsApiError } from '../../../lib/newsApi';

interface ArticlePageProps {
  params: {
    id: string;
  };
}

export const dynamic = 'force-dynamic';

async function findArticle(id: string) {
  if (!isNewsApiConfigured()) {
    return ARTICLES.find((article) => article.id === id) || null;
  }
  try {
    return await getArticle(id);
  } catch (error) {
    if (error instanceof NewsApiError && error.status === 404) {
      return null;
    }
    throw error;
  }
}

export async function generateMetadata({ params }: ArticlePageProps) {
  const article = await findArticle(params.id);
  if (!article) return { title: 'Article Not Found' };

  return {
    title: `${article.title} | N-NEWS Chronicle`,
    description: article.excerpt,
  };
}

export default async function ArticlePage({ params }: ArticlePageProps) {
  const article = await findArticle(params.id);

  if (!article) {
    notFound();
  }

  const articles = isNewsApiConfigured() ? await getArticles() : ARTICLES;
  const relatedArticles = articles.filter(
    (a) => a.id !== article.id && (a.category === article.category || a.tags.some((t) => article.tags.includes(t)))
  ).slice(0, 3);

  return <ArticleClientView article={article} relatedArticles={relatedArticles} />;
}
