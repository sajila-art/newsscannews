'use client';

import React, { useState, useEffect } from 'react';
import Header from '../components/Header';
import Footer from '../components/Footer';
import SearchModal from '../components/SearchModal';
import { CATEGORIES_LIST } from '../data/mockData';
import { getCategories, isNewsApiConfigured } from '../lib/newsApi';
import { CategoryItem } from '../types/news';

export default function LayoutClientWrapper({
  children,
}: {
  children: React.ReactNode;
}) {
  const [isSearchOpen, setIsSearchOpen] = useState(false);
  const [categories, setCategories] = useState<CategoryItem[]>(CATEGORIES_LIST);
  const [categoryLoadError, setCategoryLoadError] = useState('');

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setIsSearchOpen((prev) => !prev);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  useEffect(() => {
    if (!isNewsApiConfigured()) return;

    getCategories()
      .then(setCategories)
      .catch((error: Error) => setCategoryLoadError(error.message));
  }, []);

  return (
    <>
      <Header onOpenSearch={() => setIsSearchOpen(true)} categories={categories} />
      {categoryLoadError && (
        <p role="alert" className="bg-red-50 px-4 py-2 text-center text-sm text-red-700">
          Category data could not be loaded: {categoryLoadError}
        </p>
      )}
      <div className="flex-1">{children}</div>
      <Footer categories={categories} />
      <SearchModal isOpen={isSearchOpen} onClose={() => setIsSearchOpen(false)} />
    </>
  );
}
