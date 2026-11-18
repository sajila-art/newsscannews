'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Play, Pause, Volume2, Mail, CheckCircle2, TrendingUp, Sparkles, Briefcase } from 'lucide-react';
import { Article } from '../types/news';
import { TodaysAudioData } from '../lib/newsApi';
import { useMalayalamSpeech } from '../lib/useMalayalamSpeech';

interface SidebarTrendingProps {
  articles: Article[];
  todaysAudio?: TodaysAudioData | null;
}

export default function SidebarTrending({ articles, todaysAudio }: SidebarTrendingProps) {
  const [email, setEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);
  const [subscribing, setSubscribing] = useState(false);
  const [subscribeError, setSubscribeError] = useState<string | null>(null);

  const speechText =
    todaysAudio?.scriptText ||
    articles
      .slice(0, 5)
      .map((a, idx) => `${idx + 1}. ${a.title}.`)
      .join('\n');

  const {
    isPlaying: isPlayingAudio,
    progressPct,
    elapsedLabel,
    toggle: toggleAudio,
  } = useMalayalamSpeech({
    text: speechText,
    audioUrl: todaysAudio?.audioUrl,
  });

  const trendingList = articles
    .slice()
    .sort((a, b) => (b.views || 0) - (a.views || 0))
    .slice(0, 5);

  const jobArticles = articles.filter((a) => a.categorySlug === 'jobs' || a.category === 'ജോലി ഒഴിവുകൾ');

  const handleSubscribe = async (e: React.FormEvent) => {
    e.preventDefault();
    const trimmedEmail = email.trim();
    if (!trimmedEmail || subscribing) return;

    setSubscribing(true);
    setSubscribeError(null);
    try {
      const res = await fetch('/api/subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: trimmedEmail }),
      });
      const data = await res.json().catch(() => null);
      if (!res.ok) {
        setSubscribeError(data?.error || 'സബ്‌സ്‌ക്രിപ്‌ഷൻ പരാജയപ്പെട്ടു. വീണ്ടും ശ്രമിക്കുക.');
        return;
      }
      setSubscribed(true);
      setEmail('');
    } catch {
      setSubscribeError('സെർവറുമായി ബന്ധപ്പെടാൻ കഴിഞ്ഞില്ല. വീണ്ടും ശ്രമിക്കുക.');
    } finally {
      setSubscribing(false);
    }
  };

  return (
    <aside className="space-y-8">
      {/* Today's Audio News Player */}
      <div className="bg-gradient-to-br from-stone-900 to-stone-950 text-white p-5 rounded-xl border border-stone-800 shadow-md">
        <div className="flex items-center justify-between mb-3">
          <div className="flex items-center gap-1.5 text-red-400 text-xs font-bold">
            <Volume2 className={`w-4 h-4 ${isPlayingAudio ? 'animate-pulse' : ''}`} />
            <span>ഓഡിയോ ന്യൂസ് ബുള്ളറ്റിൻ</span>
          </div>
          <span className="text-[10px] bg-stone-800 text-stone-300 px-2 py-0.5 rounded font-mono">
            {todaysAudio?.durationLabel || '00:00'}
          </span>
        </div>

        <h4 className="font-serif text-base font-bold mb-4">
          {todaysAudio?.title || 'ഇന്നത്തെ പ്രധാന വാർത്തകൾ കേൾക്കാം'}
        </h4>

        <div className="flex items-center gap-3 bg-stone-800/80 p-2.5 rounded-lg border border-stone-700">
          <button
            onClick={toggleAudio}
            className="w-10 h-10 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition flex-shrink-0"
            aria-label={isPlayingAudio ? 'ഓഡിയോ നിർത്തുക' : 'മലയാളം ഓഡിയോ കേൾക്കുക'}
          >
            {isPlayingAudio ? <Pause className="w-4 h-4" /> : <Play className="w-4 h-4 ml-0.5" />}
          </button>
          <div className="flex-1">
            <div className="flex justify-between text-[11px] text-stone-400 mb-1 font-mono">
              <span>{elapsedLabel}</span>
              <span>{todaysAudio?.durationLabel || '00:00'}</span>
            </div>
            <div className="w-full bg-stone-700 h-1.5 rounded-full overflow-hidden">
              <div
                className="bg-red-500 h-full transition-all duration-300"
                style={{ width: `${progressPct}%` }}
              />
            </div>
          </div>
        </div>
      </div>

      {/* Most Read / Trending List */}
      <div className="bg-white dark:bg-stone-900 p-5 rounded-xl border border-stone-200 dark:border-stone-800 shadow-sm">
        <div className="flex items-center justify-between border-b border-stone-200 dark:border-stone-800 pb-3 mb-4">
          <h4 className="font-serif text-base font-bold text-stone-900 dark:text-stone-100 flex items-center gap-2">
            <TrendingUp className="w-4 h-4 text-red-600" />
            <span>കൂടുതൽ വായിക്കപ്പെട്ടവ</span>
          </h4>
          <span className="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Top 5</span>
        </div>

        <div className="space-y-4">
          {trendingList.map((art, idx) => (
            <div key={art.id} className="flex items-start gap-3 group">
              <span className="font-serif text-2xl font-black text-stone-300 dark:text-stone-700 group-hover:text-red-600 transition-colors w-6 flex-shrink-0">
                0{idx + 1}
              </span>
              <div className="flex-1">
                <span className="text-[10px] font-bold text-red-700 dark:text-red-400">
                  {art.category}
                </span>
                <Link
                  href={`/article/${art.id}`}
                  className="block text-xs sm:text-sm font-semibold text-stone-900 dark:text-stone-100 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors leading-snug mt-0.5"
                >
                  {art.title}
                </Link>
                <div className="flex items-center gap-2 mt-1 text-[11px] text-stone-400">
                  <span>{art.readTime}</span>
                  <span>•</span>
                  <span>{art.views?.toLocaleString()} വായനക്കാർ</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Quick Job Alert Pill */}
      {jobArticles.length > 0 && (
        <div className="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 p-4 rounded-xl">
          <div className="flex items-center gap-2 mb-2 text-amber-800 dark:text-amber-400 font-bold text-xs">
            <Briefcase className="w-4 h-4" />
            <span>തൊഴിൽ വാർത്തകൾ (Job Alerts)</span>
          </div>
          <Link
            href={`/article/${jobArticles[0].id}`}
            className="text-xs font-semibold text-stone-900 dark:text-stone-100 hover:text-red-600 block mb-2 leading-snug"
          >
            {jobArticles[0].title}
          </Link>
          <Link
            href="/categories/jobs"
            className="text-[11px] font-bold text-red-700 dark:text-red-400 hover:underline inline-block"
          >
            എല്ലാ തൊഴിൽ ഒഴിവുകളും കാണുക →
          </Link>
        </div>
      )}

      {/* Scanned Edition Archival Notice */}
      <div className="bg-stone-50 dark:bg-stone-900/60 p-5 rounded-xl border border-dashed border-stone-300 dark:border-stone-700 text-center">
        <Sparkles className="w-6 h-6 text-amber-500 mx-auto mb-2" />
        <h5 className="font-serif text-sm font-bold text-stone-900 dark:text-stone-100">
          ഡിജിറ്റൽ ആർക്കൈവ്സ്
        </h5>
        <p className="text-xs text-stone-500 dark:text-stone-400 mt-1 mb-3">
          പഴയ ലക്കങ്ങളുടെ N-News e-paper സ്കാൻ പതിപ്പുകൾ വായിക്കാൻ ആർക്കൈവ്സ് സന്ദർശിക്കൂ.
        </p>
        <Link
          href="/scan-news"
          className="inline-block text-xs font-bold text-red-700 dark:text-red-400 hover:underline"
        >
          പഴയ പതിപ്പുകൾ കാണുക →
        </Link>
      </div>

      {/* Newsletter Signup */}
      <div className="bg-stone-100 dark:bg-stone-800/80 p-5 rounded-xl border border-stone-200 dark:border-stone-700">
        <Mail className="w-5 h-5 text-red-600 mb-2" />
        <h5 className="font-serif text-base font-bold text-stone-900 dark:text-stone-100">
          പ്രഭാത N-News e-paper ന്യൂസ്‌ലെറ്റർ
        </h5>
        <p className="text-xs text-stone-600 dark:text-stone-400 mt-1 mb-4">
          ദിവസവും രാവിലെ 6:00 മണിക്ക് ഇന്നത്തെ സ്കാൻ ചെയ്ത പത്രവും പ്രധാന വാർത്തകളും നിങ്ങളുടെ ഇമെയിലിൽ സൗജന്യമായി ലഭിക്കാൻ സബ്സ്ക്രൈബ് ചെയ്യുക.
        </p>

        {subscribed ? (
          <div className="flex items-center gap-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 p-3 rounded-lg border border-emerald-200 dark:border-emerald-900">
            <CheckCircle2 className="w-4 h-4" />
            <span>നന്ദി! ഇന്നത്തെ പതിപ്പ് അയച്ചു നൽകുന്നതാണ്.</span>
          </div>
        ) : (
          <form onSubmit={handleSubscribe} className="space-y-2">
            <input
              type="email"
              value={email}
              onChange={(e) => {
                setEmail(e.target.value);
                if (subscribeError) setSubscribeError(null);
              }}
              placeholder="നിങ്ങളുടെ ഇമെയിൽ വിലാസം"
              required
              disabled={subscribing}
              className="w-full text-xs px-3 py-2 rounded-lg bg-white dark:bg-stone-900 border border-stone-300 dark:border-stone-700 text-stone-900 dark:text-stone-100 placeholder-stone-400 focus:outline-none focus:ring-1 focus:ring-red-600 disabled:opacity-60"
            />
            {subscribeError && (
              <p className="text-[11px] font-medium text-red-600 dark:text-red-400">
                {subscribeError}
              </p>
            )}
            <button
              type="submit"
              disabled={subscribing}
              className="w-full py-2 px-3 text-xs font-bold rounded-lg bg-red-700 hover:bg-red-800 disabled:opacity-60 text-white transition"
            >
              {subscribing ? 'സേവ് ചെയ്യുന്നു...' : 'സൗജന്യമായി സബ്‌സ്‌ക്രൈബ് ചെയ്യുക'}
            </button>
          </form>
        )}
      </div>
    </aside>
  );
}
