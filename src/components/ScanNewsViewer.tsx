'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import {
  ZoomIn,
  ZoomOut,
  ChevronLeft,
  ChevronRight,
  Maximize2,
  Minimize2,
  Scissors,
  BookOpen,
  Info,
} from 'lucide-react';
import { SCANNED_PAGES, CURRENT_EDITIONS, ARTICLES } from '../data/mockData';
import { ScanPage, ClippingZone, Article } from '../types/news';
import ClippingModal from './ClippingModal';

export default function ScanNewsViewer() {
  const searchParams = useSearchParams();
  const initialPage = Number(searchParams?.get('page')) || 1;

  const [currentPageNum, setCurrentPageNum] = useState(
    initialPage >= 1 && initialPage <= SCANNED_PAGES.length ? initialPage : 1
  );
  const [zoomLevel, setZoomLevel] = useState(1);
  const [showClippingBoxes, setShowClippingBoxes] = useState(true);
  const [selectedClipping, setSelectedClipping] = useState<ClippingZone | null>(null);
  const [activeEdition, setActiveEdition] = useState(CURRENT_EDITIONS[0].id);
  const [isFullscreen, setIsFullscreen] = useState(false);

  useEffect(() => {
    const p = Number(searchParams?.get('page'));
    if (p && p >= 1 && p <= SCANNED_PAGES.length) {
      setCurrentPageNum(p);
    }
  }, [searchParams]);

  const currentPage: ScanPage =
    SCANNED_PAGES.find((p) => p.pageNumber === currentPageNum) || SCANNED_PAGES[0];

  const handleZoomIn = () => setZoomLevel((z) => Math.min(z + 0.25, 2.5));
  const handleZoomOut = () => setZoomLevel((z) => Math.max(z - 0.25, 0.75));
  const handleResetZoom = () => setZoomLevel(1);

  const handlePrevPage = () => {
    if (currentPageNum > 1) setCurrentPageNum((p) => p - 1);
  };

  const handleNextPage = () => {
    if (currentPageNum < SCANNED_PAGES.length) setCurrentPageNum((p) => p + 1);
  };

  const selectedArticle: Article | undefined = selectedClipping
    ? ARTICLES.find((a) => a.id === selectedClipping.articleId)
    : undefined;

  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
      setIsFullscreen(true);
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen().catch(() => {});
        setIsFullscreen(false);
      }
    }
  };

  return (
    <div className="w-full bg-stone-950 text-stone-100 min-h-screen flex flex-col font-sans">
      {/* Top Controls Toolbar */}
      <div className="bg-stone-900 border-b border-stone-800 px-4 py-2.5 sticky top-0 z-30 flex flex-wrap items-center justify-between gap-3 shadow-md">
        {/* Left: Edition & Date */}
        <div className="flex items-center gap-3">
          <div className="flex items-center gap-2">
            <span className="p-1.5 bg-red-600 rounded text-white font-bold text-xs">
              <BookOpen className="w-4 h-4" />
            </span>
            <div>
              <select
                value={activeEdition}
                onChange={(e) => setActiveEdition(e.target.value)}
                className="bg-stone-800 text-stone-100 text-xs font-semibold rounded px-2 py-1 border border-stone-700 focus:outline-none focus:ring-1 focus:ring-red-500 cursor-pointer"
              >
                {CURRENT_EDITIONS.map((ed) => (
                  <option key={ed.id} value={ed.id}>
                    {ed.name}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </div>

        {/* Center: Page Navigation */}
        <div className="flex items-center gap-2 bg-stone-800 px-3 py-1 rounded-lg border border-stone-700">
          <button
            onClick={handlePrevPage}
            disabled={currentPageNum <= 1}
            className="p-1 rounded text-stone-300 hover:text-white disabled:opacity-30 disabled:hover:text-stone-300 transition"
            title="മുമ്പത്തെ പേജ്"
          >
            <ChevronLeft className="w-4 h-4" />
          </button>

          <span className="text-xs font-medium text-stone-200">
            പേജ് <span className="font-bold text-red-400">{currentPageNum}</span> / {SCANNED_PAGES.length}
          </span>

          <button
            onClick={handleNextPage}
            disabled={currentPageNum >= SCANNED_PAGES.length}
            className="p-1 rounded text-stone-300 hover:text-white disabled:opacity-30 disabled:hover:text-stone-300 transition"
            title="അടുത്ത പേജ്"
          >
            <ChevronRight className="w-4 h-4" />
          </button>
        </div>

        {/* Right: Zoom & Clipping Highlights */}
        <div className="flex items-center gap-2">
          {/* Highlight toggle */}
          <button
            onClick={() => setShowClippingBoxes(!showClippingBoxes)}
            className={`flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded transition border ${
              showClippingBoxes
                ? 'bg-red-950/80 text-red-300 border-red-700'
                : 'bg-stone-800 text-stone-400 border-stone-700'
            }`}
            title="വാർത്താ ബോക്സുകൾ കാണിക്കുക / മറയ്ക്കുക"
          >
            <Scissors className="w-3.5 h-3.5" />
            <span className="hidden sm:inline">ക്ലിപ്പിംഗ് ബോക്സ്</span>
          </button>

          {/* Zoom controls */}
          <div className="flex items-center bg-stone-800 rounded border border-stone-700">
            <button
              onClick={handleZoomOut}
              className="p-1.5 text-stone-300 hover:text-white transition"
              title="ചെറുതാക്കുക (Zoom Out)"
            >
              <ZoomOut className="w-3.5 h-3.5" />
            </button>
            <button
              onClick={handleResetZoom}
              className="px-2 py-1 text-[11px] font-mono font-medium text-stone-300 hover:text-white transition"
              title="യഥാർത്ഥ വലിപ്പം (100%)"
            >
              {Math.round(zoomLevel * 100)}%
            </button>
            <button
              onClick={handleZoomIn}
              className="p-1.5 text-stone-300 hover:text-white transition"
              title="വലുതാക്കുക (Zoom In)"
            >
              <ZoomIn className="w-3.5 h-3.5" />
            </button>
          </div>

          {/* Fullscreen */}
          <button
            onClick={toggleFullscreen}
            className="p-1.5 bg-stone-800 hover:bg-stone-700 text-stone-300 hover:text-white rounded border border-stone-700 transition"
            title={isFullscreen ? 'ഫുൾ സ്ക്രീൻ നിർത്തുക' : 'ഫുൾ സ്ക്രീൻ'}
          >
            {isFullscreen ? <Minimize2 className="w-4 h-4" /> : <Maximize2 className="w-4 h-4" />}
          </button>
        </div>
      </div>

      {/* Sub-bar: Instructions & Page Name */}
      <div className="bg-stone-900/60 border-b border-stone-800/80 px-4 py-1.5 text-[11px] flex items-center justify-between text-stone-400">
        <span className="flex items-center gap-1.5 font-medium text-stone-300">
          <Info className="w-3.5 h-3.5 text-amber-400" />
          <span>{currentPage.title}</span>
        </span>
        <span className="hidden md:inline text-stone-400">
          💡 സൂചന: സ്കാൻ പേജിൽ അടയാളപ്പെടുത്തിയിരിക്കുന്ന ബോക്സുകളിൽ ക്ലിക്ക് ചെയ്ത് വാർത്ത വായിക്കാം അല്ലെങ്കിൽ ഓഡിയോ കേൾക്കാം.
        </span>
      </div>

      {/* Main Canvas Area */}
      <div className="flex-1 overflow-auto p-4 sm:p-8 flex items-center justify-center bg-stone-950">
        <div
          className="relative transition-transform duration-200 origin-top shadow-2xl rounded-sm border border-stone-800 max-w-4xl w-full"
          style={{ transform: `scale(${zoomLevel})` }}
        >
          {/* Scanned Newspaper Image */}
          <img
            src={currentPage.fullImageUrl}
            alt={currentPage.title}
            className="w-full h-auto object-contain block select-none"
          />

          {/* Interactive Clipping Hotspot Overlays */}
          {showClippingBoxes &&
            currentPage.clippings.map((clip) => (
              <button
                key={clip.id}
                onClick={() => setSelectedClipping(clip)}
                style={{
                  top: `${clip.topPercent}%`,
                  left: `${clip.leftPercent}%`,
                  width: `${clip.widthPercent}%`,
                  height: `${clip.heightPercent}%`,
                }}
                className="absolute border-2 border-red-500/60 bg-red-500/10 hover:bg-red-500/25 hover:border-red-500 rounded transition-all cursor-pointer group flex items-start justify-end p-1.5 shadow-sm"
                title={`വാർത്ത വായിക്കുക: "${clip.headline}"`}
              >
                {/* Hotspot Scissor icon badge */}
                <span className="opacity-0 group-hover:opacity-100 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow flex items-center gap-1 transition-opacity">
                  <Scissors className="w-3 h-3" />
                  വാർത്ത വായിക്കൂ
                </span>
              </button>
            ))}
        </div>
      </div>

      {/* Bottom Thumbnail Strip */}
      <div className="bg-stone-900 border-t border-stone-800 p-3 sticky bottom-0 z-20">
        <div className="max-w-5xl mx-auto flex items-center justify-center gap-3 overflow-x-auto">
          {SCANNED_PAGES.map((page) => (
            <button
              key={page.pageNumber}
              onClick={() => setCurrentPageNum(page.pageNumber)}
              className={`relative flex-shrink-0 w-16 sm:w-20 aspect-[3/4] rounded overflow-hidden border-2 transition-all ${
                currentPageNum === page.pageNumber
                  ? 'border-red-500 scale-105 shadow-md shadow-red-900/30'
                  : 'border-stone-700 opacity-60 hover:opacity-100 hover:border-stone-500'
              }`}
            >
              <img
                src={page.thumbnail}
                alt={page.title}
                className="w-full h-full object-cover"
              />
              <span className="absolute bottom-0 inset-x-0 bg-stone-950/90 text-[10px] font-bold text-center text-stone-200 py-0.5">
                പേജ് {page.pageNumber}
              </span>
            </button>
          ))}
        </div>
      </div>

      {/* Reader Modal for Selected Clipping */}
      {selectedClipping && (
        <ClippingModal
          clipping={selectedClipping}
          article={selectedArticle}
          onClose={() => setSelectedClipping(null)}
        />
      )}
    </div>
  );
}
