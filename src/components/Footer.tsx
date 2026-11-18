import React from 'react';
import Link from 'next/link';
import { ShieldCheck } from 'lucide-react';
import { CATEGORIES_LIST } from '../data/mockData';
import { CategoryItem } from '../types/news';

export default function Footer({ categories = CATEGORIES_LIST }: { categories?: CategoryItem[] }) {
  return (
    <footer className="bg-stone-900 text-stone-300 border-t border-stone-800 pt-12 pb-8">
      <div className="max-w-7xl mx-auto px-4">
        {/* Main Footer Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-stone-800">
          {/* Column 1: Brand & About */}
          <div className="lg:col-span-2 space-y-4">
            <Link href="/" className="inline-flex items-center gap-3">
              <img
                src="/logo.jpg"
                alt="N-NEWS Logo"
                className="w-12 h-12 rounded-full object-cover border border-stone-700 shadow-sm"
              />
              <div>
                <h2 className="font-serif text-3xl font-black text-stone-100 tracking-tight leading-none">
                  N-NEWS
                </h2>
                <p className="text-xs uppercase tracking-widest text-red-500 font-bold mt-1">
                  എൻ-ന്യൂസ് • സ്കാൻ ന്യൂസ് & News Portal
                </p>
              </div>
            </Link>
            <p className="text-xs text-stone-400 leading-relaxed max-w-sm">
              കേരളത്തിലെയും ദേശ-വിദേശങ്ങളിലെയും സമഗ്ര വാർത്തകളും അച്ചടി പത്രത്തിന്റെ അതേ രൂപത്തിലുള്ള ഹൈ-ഡെഫനിഷൻ ഇ-പേപ്പർ സ്കാൻ പതിപ്പും വായനക്കാരിലേക്ക് എത്തിക്കുന്ന ആധുനിക ന്യൂസ് പോർട്ടൽ.
            </p>
            <div className="flex items-center gap-3 text-xs text-stone-400">
              <span className="flex items-center gap-1">
                <ShieldCheck className="w-4 h-4 text-emerald-500" />
                സ്വതന്ത്ര മാധ്യമപ്രവർത്തനം
              </span>
              <span>•</span>
              <span>RNI / ISSN 2455-8910</span>
            </div>
          </div>

          {/* Column 2: Digital News Sections (1-6) */}
          <div>
            <h4 className="font-serif text-sm font-bold text-stone-100 uppercase tracking-wider mb-3">
              വാർത്താ വിഭാഗങ്ങൾ
            </h4>
            <ul className="space-y-2 text-xs">
              {categories.slice(0, 6).map((cat) => (
                <li key={cat.id}>
                  <Link
                    href={`/categories/${cat.id}`}
                    className="hover:text-red-400 transition"
                  >
                    {cat.nameMalayalam}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Column 3: Digital News Sections (7-12) */}
          <div>
            <h4 className="font-serif text-sm font-bold text-stone-100 uppercase tracking-wider mb-3">
              പ്രത്യേക പംക്തികൾ
            </h4>
            <ul className="space-y-2 text-xs">
              {categories.slice(6, 12).map((cat) => (
                <li key={cat.id}>
                  <Link
                    href={`/categories/${cat.id}`}
                    className="hover:text-red-400 transition"
                  >
                    {cat.nameMalayalam}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

        </div>

        {/* Bottom Credits */}
        <div className="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-500 gap-4">
          <p>© {new Date().getFullYear()} N-NEWS & Scan Media. All rights reserved.</p>
          <div className="flex items-center gap-4">
            <span className="hover:text-stone-400 cursor-pointer">സൈറ്റ്മാപ്പ്</span>
            <span className="hover:text-stone-400 cursor-pointer">പരസ്യങ്ങൾക്ക്</span>
            <span className="hover:text-stone-400 cursor-pointer">സ്വകാര്യതാനയം</span>
            <span className="hover:text-stone-400 cursor-pointer">ബന്ധപ്പെടുക</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
