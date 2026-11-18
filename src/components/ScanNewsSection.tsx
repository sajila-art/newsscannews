'use client';

import React from 'react';
import Link from 'next/link';
import { BookOpen, ZoomIn, Scissors, Sparkles, ArrowRight, Layers, Download } from 'lucide-react';
import { SCANNED_PAGES, CURRENT_EDITIONS } from '../data/mockData';

export default function ScanNewsSection() {
  return (
    <section className="my-12 py-10 px-6 sm:px-8 bg-stone-900 text-stone-100 rounded-2xl shadow-xl overflow-hidden relative border border-stone-800">
      {/* Background texture styling */}
      <div className="absolute inset-0 opacity-5 pointer-events-none bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px]"></div>

      {/* Top Banner Header */}
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8 border-b border-stone-800 pb-6 relative z-10">
        <div>
          <div className="flex items-center gap-2 mb-2">
            <span className="flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-black bg-red-600 text-white">
              <Sparkles className="w-3 h-3" />
              ഡിജിറ്റൽ അച്ചടി പകർപ്പ്
            </span>
            <span className="text-xs text-stone-400">High-Resolution 300 DPI Scans</span>
          </div>
          <h2 className="font-serif text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-white">
            ഇന്നത്തെ N-News e-paper സ്കാൻ പതിപ്പ്
          </h2>
          <p className="text-stone-400 text-xs sm:text-sm mt-1 max-w-xl">
            അച്ചടി പത്രത്തിന്റെ അതേ രൂപത്തിൽ പേജ് ബൈ പേജ് വായിക്കാം. ആവശ്യമുള്ള വാർത്താ ഭാഗങ്ങളിൽ ക്ലിക്ക് ചെയ്ത് സൂം ചെയ്യാനും ടെക്സ്റ്റ് വായിക്കാനും ഓഡിയോ കേൾക്കാനും സാധിക്കും.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <Link
            href="/scan-news"
            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold text-xs sm:text-sm tracking-wide transition shadow-lg shadow-red-950/40"
          >
            <BookOpen className="w-4 h-4" />
            <span>N-News e-paper റീഡർ തുറക്കുക</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>

      {/* Feature Highlights Pills */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8 text-xs text-stone-300 relative z-10">
        <div className="flex items-center gap-2.5 bg-stone-800/60 p-3 rounded-lg border border-stone-700/60">
          <Scissors className="w-4 h-4 text-red-400 flex-shrink-0" />
          <div>
            <p className="font-semibold text-white">സ്മാർട്ട് ക്ലിപ്പിംഗ്</p>
            <p className="text-[11px] text-stone-400">ബോക്സുകളിൽ ക്ലിക്ക് ചെയ്ത് വായിക്കാം</p>
          </div>
        </div>
        <div className="flex items-center gap-2.5 bg-stone-800/60 p-3 rounded-lg border border-stone-700/60">
          <ZoomIn className="w-4 h-4 text-amber-400 flex-shrink-0" />
          <div>
            <p className="font-semibold text-white">സൂം & പാൻ സൗകര്യം</p>
            <p className="text-[11px] text-stone-400">അക്ഷരങ്ങൾ വ്യക്തമായി കാണാം</p>
          </div>
        </div>
        <div className="flex items-center gap-2.5 bg-stone-800/60 p-3 rounded-lg border border-stone-700/60">
          <Layers className="w-4 h-4 text-emerald-400 flex-shrink-0" />
          <div>
            <p className="font-semibold text-white">6 സമ്പൂർണ്ണ പേജുകൾ</p>
            <p className="text-[11px] text-stone-400">ക്രൈം, ബിസിനസ്, കരിയർ, സാഹിത്യം</p>
          </div>
        </div>
        <div className="flex items-center gap-2.5 bg-stone-800/60 p-3 rounded-lg border border-stone-700/60">
          <Download className="w-4 h-4 text-sky-400 flex-shrink-0" />
          <div>
            <p className="font-semibold text-white">ഡൗൺലോഡ് & ഷെയർ</p>
            <p className="text-[11px] text-stone-400">ക്ലിപ്പിംഗുകൾ സുഹൃത്തുക്കൾക്ക് പങ്കുവെക്കാം</p>
          </div>
        </div>
      </div>

      {/* Scanned Pages Carousel / Grid */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 relative z-10">
        {SCANNED_PAGES.map((page) => (
          <Link
            key={page.pageNumber}
            href={`/scan-news?page=${page.pageNumber}`}
            className="group relative flex flex-col bg-stone-800 rounded-lg overflow-hidden border border-stone-700 hover:border-red-500 transition-all duration-300 transform hover:-translate-y-1.5 shadow-md hover:shadow-xl"
          >
            {/* Page Preview Image */}
            <div className="relative aspect-[3/4] w-full bg-stone-900 overflow-hidden">
              <img
                src={page.thumbnail}
                alt={page.title}
                className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 opacity-90 group-hover:opacity-100"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-stone-950/80 via-transparent to-black/20 group-hover:from-stone-950/90 transition-opacity" />

              {/* Page Number badge */}
              <div className="absolute top-2 left-2">
                <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-900/90 text-stone-100 border border-stone-700">
                  പേജ് {page.pageNumber}
                </span>
              </div>

              {/* Hover overlay hint */}
              <div className="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 text-center bg-stone-950/60 backdrop-blur-[2px]">
                <ZoomIn className="w-6 h-6 text-red-400 mb-1" />
                <span className="text-[11px] font-bold text-white">
                  പേജ് തുറക്കുക
                </span>
                <span className="text-[9px] text-stone-300">
                  {page.clippings.length} പ്രധാന വാർത്തകൾ
                </span>
              </div>
            </div>

            {/* Page caption */}
            <div className="p-2.5 bg-stone-800/90 flex flex-col justify-between flex-1">
              <p className="text-[11px] font-semibold text-stone-200 truncate">
                {page.title.split('—')[1] || page.title}
              </p>
              <div className="flex items-center justify-between text-[10px] text-stone-400 mt-1">
                <span>{page.clippings.length} വാർത്തകൾ</span>
                <span className="text-red-400 font-medium group-hover:underline">വായിക്കുക →</span>
              </div>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
