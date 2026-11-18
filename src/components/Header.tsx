'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import {
  Search,
  Sun,
  Moon,
  CloudSun,
  Calendar,
  Globe,
  Menu,
  X,
  ChevronDown,
} from 'lucide-react';
import { CURRENT_EDITIONS, MARKET_INDICES, CATEGORIES_LIST } from '../data/mockData';
import { CategoryItem, MarketIndex } from '../types/news';

interface HeaderProps {
  onOpenSearch?: () => void;
  categories?: CategoryItem[];
}

interface LiveWeatherState {
  cityMl: string;
  tempC: number;
  aqi: number | null;
  aqiLabelMl: string;
}

const MALAYALAM_MONTHS = [
  'ജനുവരി',
  'ഫെബ്രുവരി',
  'മാർച്ച്',
  'ഏപ്രിൽ',
  'മേയ്',
  'ജൂൺ',
  'ജൂലൈ',
  'ഓഗസ്റ്റ്',
  'സെപ്റ്റംബർ',
  'ഒക്ടോബർ',
  'നവംബർ',
  'ഡിസംബർ',
];

const MALAYALAM_WEEKDAYS: Record<string, string> = {
  Sun: 'ഞായർ',
  Mon: 'തിങ്കൾ',
  Tue: 'ചൊവ്വ',
  Wed: 'ബുധൻ',
  Thu: 'വ്യാഴം',
  Fri: 'വെള്ളി',
  Sat: 'ശനി',
};

function formatMalayalamDate(date: Date): string {
  const parts = new Intl.DateTimeFormat('en-US', {
    timeZone: 'Asia/Kolkata',
    year: 'numeric',
    month: 'numeric',
    day: 'numeric',
    weekday: 'short',
  }).formatToParts(date);

  const get = (type: string) => parts.find((p) => p.type === type)?.value || '';
  const year = get('year');
  const monthIdx = Math.max(0, Math.min(11, Number(get('month')) - 1));
  const day = get('day');
  const weekday = MALAYALAM_WEEKDAYS[get('weekday')] || '';

  return `${year} ${MALAYALAM_MONTHS[monthIdx]} ${day}, ${weekday}`;
}

export default function Header({ onOpenSearch, categories = CATEGORIES_LIST }: HeaderProps) {
  const [isDarkMode, setIsDarkMode] = useState(false);
  const [selectedEdition, setSelectedEdition] = useState(CURRENT_EDITIONS[0].id);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [dateLabel, setDateLabel] = useState<string>(() => formatMalayalamDate(new Date()));
  const [weather, setWeather] = useState<LiveWeatherState>({
    cityMl: 'കൊച്ചി',
    tempC: 28,
    aqi: 42,
    aqiLabelMl: 'നല്ലത്',
  });
  const [marketIndices, setMarketIndices] = useState<MarketIndex[]>(MARKET_INDICES.slice(0, 3));
  const pathname = usePathname();

  // Top menu condition: only display categories where home_page != 1
  const topMenuCategories = categories.filter((cat) => {
    const hp = (cat as any).home_page !== undefined ? (cat as any).home_page : cat.homePage;
    return Number(hp) !== 1 && hp !== true;
  });

  useEffect(() => {
    const savedTheme = localStorage.getItem('enews-theme');
    if (savedTheme === 'dark') {
      setIsDarkMode(true);
      document.documentElement.classList.add('dark');
    }

    setDateLabel(formatMalayalamDate(new Date()));
    const timer = setInterval(() => {
      setDateLabel(formatMalayalamDate(new Date()));
    }, 60_000);
    return () => clearInterval(timer);
  }, []);

  useEffect(() => {
    let cancelled = false;

    const loadLiveHeader = async () => {
      try {
        const res = await fetch(`/api/header-live?edition=${encodeURIComponent(selectedEdition)}`, {
          cache: 'no-store',
        });
        if (!res.ok) return;
        const data = await res.json();
        if (cancelled) return;
        if (data?.weather) {
          setWeather(data.weather);
        }
        if (Array.isArray(data?.market) && data.market.length > 0) {
          setMarketIndices(data.market);
        }
      } catch {
        // Keep current fallback values
      }
    };

    loadLiveHeader();
    const refreshTimer = setInterval(loadLiveHeader, 5 * 60_000);
    return () => {
      cancelled = true;
      clearInterval(refreshTimer);
    };
  }, [selectedEdition]);

  const toggleDarkMode = () => {
    if (isDarkMode) {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('enews-theme', 'light');
      setIsDarkMode(false);
    } else {
      document.documentElement.classList.add('dark');
      localStorage.setItem('enews-theme', 'dark');
      setIsDarkMode(true);
    }
  };

  return (
    <header className="w-full bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800 transition-colors">
      {/* Top Utility Bar */}
      <div className="bg-stone-100 dark:bg-stone-950 text-stone-600 dark:text-stone-400 text-xs border-b border-stone-200 dark:border-stone-800">
        <div className="max-w-7xl mx-auto px-4 py-1.5 flex flex-wrap items-center justify-between gap-2">
          {/* Date & Weather */}
          <div className="flex items-center gap-3">
            <span className="flex items-center gap-1.5 font-medium" suppressHydrationWarning>
              <Calendar className="w-3.5 h-3.5 text-red-700 dark:text-red-500" />
              {dateLabel}
            </span>
            <span className="hidden sm:inline-block text-stone-300 dark:text-stone-700">|</span>
            <span className="hidden sm:flex items-center gap-1.5">
              <CloudSun className="w-3.5 h-3.5 text-amber-500" />
              {weather.cityMl}: {weather.tempC}°C
              {weather.aqi !== null ? ` • AQI ${weather.aqi} (${weather.aqiLabelMl})` : ''}
            </span>
          </div>

          {/* Market Snapshot Strip (Malayalam labels) */}
          <div className="hidden lg:flex items-center gap-4 overflow-hidden">
            <span className="text-[11px] font-semibold text-stone-500">വിപണി:</span>
            {marketIndices.slice(0, 3).map((m) => (
              <span key={m.name} className="flex items-center gap-1">
                <span className="font-semibold">{m.name}</span>
                <span className={m.isPositive ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-rose-600 dark:text-rose-400 font-medium'}>
                  {m.value} ({m.change})
                </span>
              </span>
            ))}
          </div>

          {/* Edition Selector & Fast Actions */}
          <div className="flex items-center gap-3">
            <div className="flex items-center gap-1">
              <Globe className="w-3.5 h-3.5 text-stone-500" />
              <select
                value={selectedEdition}
                onChange={(e) => setSelectedEdition(e.target.value)}
                className="bg-transparent border-0 text-xs font-medium text-stone-700 dark:text-stone-300 focus:ring-0 cursor-pointer pr-4"
              >
                {CURRENT_EDITIONS.map((ed) => (
                  <option key={ed.id} value={ed.id} className="dark:bg-stone-900">
                    {ed.name}
                  </option>
                ))}
              </select>
            </div>

            <button
              onClick={toggleDarkMode}
              title={isDarkMode ? 'ലൈറ്റ് മോഡിലേക്ക് മാറുക' : 'ഡാർക്ക് മോഡിലേക്ക് മാറുക'}
              className="p-1 rounded hover:bg-stone-200 dark:hover:bg-stone-800 text-stone-600 dark:text-stone-300 transition-colors"
            >
              {isDarkMode ? <Sun className="w-3.5 h-3.5 text-amber-400" /> : <Moon className="w-3.5 h-3.5" />}
            </button>
          </div>
        </div>
      </div>

      {/* Main Masthead */}
      <div className="max-w-7xl mx-auto px-4 py-4 md:py-6">
        <div className="flex items-center justify-between">
          {/* Left Action / Search Button */}
          <div className="flex items-center gap-2">
            <button
              onClick={onOpenSearch}
              className="flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-stone-700 dark:text-stone-300 bg-stone-100 dark:bg-stone-800 hover:bg-stone-200 dark:hover:bg-stone-700 rounded border border-stone-300 dark:border-stone-700 transition"
            >
              <Search className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">വാർത്തകൾ തിരയുക</span>
              <kbd className="hidden md:inline px-1.5 py-0.5 text-[10px] bg-stone-200 dark:bg-stone-700 rounded text-stone-500">⌘K</kbd>
            </button>
          </div>

          {/* Center Brand Title */}
          <div className="text-center">
            <Link href="/" className="inline-flex flex-col items-center group">
              <div className="flex items-center justify-center gap-3">
                <img
                  src="/logo.jpg"
                  alt="N-NEWS Logo"
                  className="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 rounded-full object-cover shadow-md border border-stone-200 dark:border-stone-700"
                />
                <h1 className="font-serif text-3xl sm:text-5xl md:text-6xl font-black tracking-tight text-stone-900 dark:text-stone-100 group-hover:text-red-700 dark:group-hover:text-red-500 transition-colors">
                  N-NEWS
                </h1>
              </div>
              <div className="flex items-center justify-center gap-2 mt-1">
                <span className="h-[1px] w-6 sm:w-12 bg-red-700 dark:bg-red-500"></span>
                <span className="text-xs sm:text-sm tracking-wide font-bold text-red-700 dark:text-red-400">
                  എൻ-ന്യൂസ് • സ്കാൻ ന്യൂസ് & News Portal
                </span>
                <span className="h-[1px] w-6 sm:w-12 bg-red-700 dark:bg-red-500"></span>
              </div>
            </Link>
          </div>

          {/* Right Action: Mobile menu trigger */}
          <div className="flex items-center gap-2">
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden p-2 text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800 rounded"
              aria-label="മെനു തുറക്കുക"
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>
          </div>
        </div>
      </div>

      {/* Main Malayalam Categories Navigation Bar */}
      <nav className="border-t border-b border-stone-300 dark:border-stone-800 bg-stone-50 dark:bg-stone-900/50">
        <div className="max-w-7xl mx-auto px-4">
          {/* Desktop Horizontal Scrollable Menu */}
          <div className="hidden lg:flex items-center gap-1 py-1.5 overflow-x-auto scrollbar-none text-xs xl:text-[13px] font-bold">
            <Link
              href="/"
              className={`px-3 py-1 rounded whitespace-nowrap transition-colors ${
                pathname === '/'
                  ? 'bg-red-700 text-white'
                  : 'text-stone-800 dark:text-stone-200 hover:text-red-700 dark:hover:text-red-400'
              }`}
            >
              ഹോം
            </Link>

            {topMenuCategories.map((cat) => {
              const href = `/categories/${cat.id}`;
              const isActive = pathname === href;
              return (
                <Link
                  key={cat.id}
                  href={href}
                  className={`px-2.5 py-1 rounded whitespace-nowrap transition-colors ${
                    isActive
                      ? 'bg-red-700 text-white'
                      : 'text-stone-700 dark:text-stone-300 hover:text-red-700 dark:hover:text-red-400 hover:bg-stone-200/50 dark:hover:bg-stone-800'
                  }`}
                >
                  {cat.nameMalayalam}
                </Link>
              );
            })}
          </div>

          {/* Mobile Navigation Dropdown */}
          {mobileMenuOpen && (
            <div className="lg:hidden py-3 border-t border-stone-200 dark:border-stone-800 grid grid-cols-2 gap-1">
              <Link
                href="/"
                onClick={() => setMobileMenuOpen(false)}
                className="px-3 py-2 text-xs font-bold rounded text-stone-800 dark:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-800"
              >
                ഹോം (Home)
              </Link>
              {topMenuCategories.map((cat) => (
                <Link
                  key={cat.id}
                  href={`/categories/${cat.id}`}
                  onClick={() => setMobileMenuOpen(false)}
                  className="px-3 py-2 text-xs font-medium rounded text-stone-800 dark:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-800"
                >
                  {cat.nameMalayalam}
                </Link>
              ))}
            </div>
          )}
        </div>
      </nav>
    </header>
  );
}
