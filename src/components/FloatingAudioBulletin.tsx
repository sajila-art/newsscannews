'use client';

import React, { useState } from 'react';
import { Play, Pause, Volume2, ChevronDown, ChevronUp, Radio, BookOpen } from 'lucide-react';
import { TodaysAudioData } from '../lib/newsApi';
import { useMalayalamSpeech } from '../lib/useMalayalamSpeech';

interface FloatingAudioBulletinProps {
  todaysAudio?: TodaysAudioData | null;
}

export default function FloatingAudioBulletin({ todaysAudio }: FloatingAudioBulletinProps) {
  const [isMinimized, setIsMinimized] = useState(false);
  const [showScript, setShowScript] = useState(true);

  const scriptText = todaysAudio?.scriptText || '';
  const {
    sentences,
    isPlaying,
    activeIndex,
    progressPct,
    elapsedLabel,
    play,
    toggle,
  } = useMalayalamSpeech({
    text: scriptText,
    audioUrl: todaysAudio?.audioUrl,
  });

  if (!todaysAudio) {
    return null;
  }

  // Enforce condition: created_at == current date
  const todayStr = new Date().toISOString().slice(0, 10);
  const recordDate = (todaysAudio.createdAt || todaysAudio.audioDate || '').slice(0, 10);
  if (recordDate !== todayStr) {
    return null;
  }

  return (
    <div className="fixed bottom-4 right-4 z-40 w-[320px] sm:w-[370px] bg-stone-900 text-white rounded-xl border border-stone-700/80 shadow-2xl overflow-hidden transition-all">
      {/* Header Bar */}
      <div className="flex items-center justify-between px-3.5 py-2.5 bg-stone-950/90 border-b border-stone-800">
        <div className="flex items-center gap-2 text-xs font-bold text-red-400">
          <Radio className={`w-3.5 h-3.5 ${isPlaying ? 'animate-pulse text-red-500' : ''}`} />
          <span>ഓഡിയോ ന്യൂസ് ബുള്ളറ്റിൻ</span>
          <span className="px-1.5 py-0.5 text-[10px] bg-red-600/20 text-red-300 rounded font-mono">
            LIVE
          </span>
        </div>
        <div className="flex items-center gap-1.5">
          <button
            onClick={() => setShowScript(!showScript)}
            className={`px-2 py-0.5 text-[10px] font-semibold rounded flex items-center gap-1 transition ${
              showScript
                ? 'bg-red-600/25 text-red-300 border border-red-500/40'
                : 'bg-stone-800 text-stone-300 hover:bg-stone-700'
            }`}
            title="മലയാളം വാർത്ത വായിക്കുക"
          >
            <BookOpen className="w-3 h-3" />
            <span>വായിക്കുക</span>
          </button>
          <button
            onClick={() => setIsMinimized(!isMinimized)}
            className="p-1 text-stone-400 hover:text-white rounded hover:bg-stone-800 transition"
            aria-label={isMinimized ? 'Expand Audio Bulletin' : 'Minimize Audio Bulletin'}
          >
            {isMinimized ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
          </button>
        </div>
      </div>

      {/* Body */}
      {!isMinimized && (
        <div className="p-3.5 space-y-3">
          <div>
            <h5 className="font-serif text-xs sm:text-sm font-bold text-stone-100 line-clamp-1">
              {todaysAudio.title}
            </h5>
            <p className="text-[11px] text-stone-400 mt-0.5">
              ഇന്നത്തെ തത്സമയ വാർത്തകൾ ({todaysAudio.articleCount} വാർത്തകൾ)
            </p>
          </div>

          <div className="flex items-center gap-3 bg-stone-800/80 p-2 rounded-lg border border-stone-700/80">
            <button
              onClick={toggle}
              className="w-9 h-9 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition flex-shrink-0 shadow"
              aria-label={isPlaying ? 'ഓഡിയോ നിർത്തുക' : 'മലയാളം ഓഡിയോ കേൾക്കുക'}
            >
              {isPlaying ? <Pause className="w-4 h-4" /> : <Play className="w-4 h-4 ml-0.5" />}
            </button>
            <div className="flex-1 min-w-0">
              <div className="flex justify-between text-[10px] text-stone-400 mb-1 font-mono">
                <span>{elapsedLabel}</span>
                <span>{todaysAudio.durationLabel || '02:00'}</span>
              </div>
              <div className="w-full bg-stone-700 h-1.5 rounded-full overflow-hidden">
                <div
                  className="bg-red-500 h-full transition-all duration-300"
                  style={{ width: `${progressPct}%` }}
                />
              </div>
            </div>
            <Volume2 className={`w-4 h-4 text-stone-400 flex-shrink-0 ${isPlaying ? 'text-red-400' : ''}`} />
          </div>

          {/* Malayalam Read-Along Script Panel */}
          {showScript && sentences.length > 0 && (
            <div className="max-h-40 overflow-y-auto bg-stone-950/70 border border-stone-800 rounded-lg p-2.5 space-y-1.5 text-xs leading-relaxed">
              {sentences.map((line, idx) => {
                const isActive = idx === activeIndex;
                return (
                  <p
                    key={idx}
                    onClick={() => play(idx)}
                    className={`cursor-pointer px-2 py-1 rounded transition-colors ${
                      isActive
                        ? 'bg-red-600/25 text-white font-semibold border-l-2 border-red-500'
                        : 'text-stone-300 hover:bg-stone-800/70'
                    }`}
                  >
                    {line}
                  </p>
                );
              })}
            </div>
          )}
        </div>
      )}
    </div>
  );
}
