'use client';

import React, { useState, useEffect } from 'react';
import { Flame, ChevronLeft, ChevronRight } from 'lucide-react';
import { BREAKING_NEWS } from '../data/mockData';

export default function Ticker() {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isPaused, setIsPaused] = useState(false);

  useEffect(() => {
    if (isPaused) return;
    const interval = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % BREAKING_NEWS.length);
    }, 4500);

    return () => clearInterval(interval);
  }, [isPaused]);

  const handlePrev = () => {
    setCurrentIndex((prev) => (prev - 1 + BREAKING_NEWS.length) % BREAKING_NEWS.length);
  };

  const handleNext = () => {
    setCurrentIndex((prev) => (prev + 1) % BREAKING_NEWS.length);
  };

  return (
    <div
      className="bg-stone-900 text-stone-100 border-b border-stone-800"
      onMouseEnter={() => setIsPaused(true)}
      onMouseLeave={() => setIsPaused(false)}
    >
      <div className="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-4">
        {/* Badge */}
        <div className="flex items-center gap-2 flex-shrink-0">
          <span className="flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-black bg-red-600 text-white animate-pulse">
            <Flame className="w-3.5 h-3.5" />
            തത്സമയ വാർത്ത
          </span>
        </div>

        {/* Headline text */}
        <div className="flex-1 overflow-hidden">
          <p className="text-xs sm:text-sm font-medium tracking-wide truncate transition-opacity duration-300">
            {BREAKING_NEWS[currentIndex]}
          </p>
        </div>

        {/* Navigation Buttons */}
        <div className="flex items-center gap-1 flex-shrink-0">
          <button
            onClick={handlePrev}
            className="p-1 text-stone-400 hover:text-white hover:bg-stone-800 rounded transition"
            aria-label="മുമ്പത്തെ വാർത്ത"
          >
            <ChevronLeft className="w-4 h-4" />
          </button>
          <span className="text-[10px] text-stone-400 font-mono px-1">
            {currentIndex + 1}/{BREAKING_NEWS.length}
          </span>
          <button
            onClick={handleNext}
            className="p-1 text-stone-400 hover:text-white hover:bg-stone-800 rounded transition"
            aria-label="അടുത്ത വാർത്ത"
          >
            <ChevronRight className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  );
}
