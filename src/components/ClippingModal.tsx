'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import {
  X,
  Printer,
  Volume2,
  VolumeX,
  ArrowUpRight,
  Scissors,
} from 'lucide-react';
import { Article, ClippingZone } from '../types/news';
import ShareMenu from './ShareMenu';

interface ClippingModalProps {
  clipping: ClippingZone;
  article?: Article;
  onClose: () => void;
}

export default function ClippingModal({ clipping, article, onClose }: ClippingModalProps) {
  const [fontSize, setFontSize] = useState<'normal' | 'large' | 'xlarge'>('normal');
  const [isSpeaking, setIsSpeaking] = useState(false);

  if (!article) return null;

  const fontClasses = {
    normal: 'text-sm sm:text-base leading-relaxed',
    large: 'text-base sm:text-lg leading-relaxed',
    xlarge: 'text-lg sm:text-xl leading-relaxed',
  };

  const handleSpeech = () => {
    if ('speechSynthesis' in window) {
      if (isSpeaking) {
        window.speechSynthesis.cancel();
        setIsSpeaking(false);
      } else {
        const textToSpeak = `${article.title}. ${article.author.name} റിപ്പോർട്ട് ചെയ്യുന്നു. ${article.content.join(' ')}`;
        const utterance = new SpeechSynthesisUtterance(textToSpeak);
        utterance.lang = 'ml-IN';
        utterance.rate = 0.95;
        utterance.onend = () => setIsSpeaking(false);
        utterance.onerror = () => setIsSpeaking(false);
        window.speechSynthesis.speak(utterance);
        setIsSpeaking(true);
      }
    } else {
      alert('ബ്രൗസറിൽ ഓഡിയോ വോയിസ് പിന്തുണ ലഭ്യമല്ല.');
    }
  };

  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/80 backdrop-blur-sm animate-fadeIn">
      <div className="bg-white dark:bg-stone-900 w-full max-w-2xl max-h-[90vh] rounded-2xl shadow-2xl flex flex-col border border-stone-300 dark:border-stone-700 overflow-hidden font-sans">
        {/* Modal Header */}
        <div className="px-6 py-4 border-b border-stone-200 dark:border-stone-800 flex items-center justify-between bg-stone-50 dark:bg-stone-950">
          <div className="flex items-center gap-2">
            <span className="flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold bg-red-600 text-white rounded">
              <Scissors className="w-3 h-3" />
              N-News e-paper ക്ലിപ്പിംഗ്
            </span>
            <span className="text-xs font-semibold text-stone-600 dark:text-stone-300">
              {article.category}
            </span>
          </div>

          <div className="flex items-center gap-2">
            {/* Font size toggles */}
            <div className="hidden sm:flex items-center bg-stone-200 dark:bg-stone-800 rounded p-0.5 text-xs">
              <button
                onClick={() => setFontSize('normal')}
                className={`px-2 py-0.5 rounded font-bold ${
                  fontSize === 'normal' ? 'bg-white dark:bg-stone-700 shadow-xs' : 'text-stone-600 dark:text-stone-400'
                }`}
                title="സാധാരണ അക്ഷരവലിപ്പം"
              >
                A
              </button>
              <button
                onClick={() => setFontSize('large')}
                className={`px-2 py-0.5 rounded font-bold text-sm ${
                  fontSize === 'large' ? 'bg-white dark:bg-stone-700 shadow-xs' : 'text-stone-600 dark:text-stone-400'
                }`}
                title="വലിയ അക്ഷരങ്ങൾ"
              >
                A+
              </button>
              <button
                onClick={() => setFontSize('xlarge')}
                className={`px-2 py-0.5 rounded font-bold text-base ${
                  fontSize === 'xlarge' ? 'bg-white dark:bg-stone-700 shadow-xs' : 'text-stone-600 dark:text-stone-400'
                }`}
                title="ഏറ്റവും വലിയ അക്ഷരങ്ങൾ"
              >
                A++
              </button>
            </div>

            {/* Audio Reader */}
            <button
              onClick={handleSpeech}
              className={`p-1.5 rounded transition ${
                isSpeaking
                  ? 'bg-red-600 text-white'
                  : 'bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-300 hover:bg-stone-300'
              }`}
              title={isSpeaking ? 'ഓഡിയോ നിർത്തുക' : 'വാർത്ത കേൾക്കുക'}
            >
              {isSpeaking ? <VolumeX className="w-4 h-4" /> : <Volume2 className="w-4 h-4" />}
            </button>

            {/* Print */}
            <button
              onClick={handlePrint}
              className="p-1.5 rounded bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-300 hover:bg-stone-300 transition"
              title="ക്ലിപ്പിംഗ് പ്രിന്റ് ചെയ്യുക"
            >
              <Printer className="w-4 h-4" />
            </button>

            {/* Share */}
            <ShareMenu
              title={article.title}
              url={`/article/${article.id}`}
              buttonClassName="p-1.5 rounded bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-300 hover:bg-stone-300 transition"
            />

            {/* Close */}
            <button
              onClick={onClose}
              className="p-1.5 rounded hover:bg-stone-200 dark:hover:bg-stone-800 text-stone-500 hover:text-stone-900 dark:hover:text-stone-100 transition ml-1"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Modal Scrollable Content */}
        <div className="p-6 overflow-y-auto flex-1 space-y-4">
          <div className="border-b border-stone-200 dark:border-stone-800 pb-3">
            <h2 className="font-serif text-xl sm:text-2xl font-bold text-stone-900 dark:text-stone-100 leading-snug">
              {article.title}
            </h2>
            {article.subtitle && (
              <p className="font-serif italic text-xs sm:text-sm text-stone-600 dark:text-stone-300 mt-2">
                {article.subtitle}
              </p>
            )}

            <div className="flex items-center gap-2 mt-3 text-xs text-stone-500 dark:text-stone-400">
              <span className="font-semibold text-stone-800 dark:text-stone-200">
                {article.author.name}
              </span>
              <span>•</span>
              <span>{article.author.role}</span>
              <span>•</span>
              <span>{article.publishedAt}</span>
            </div>
          </div>

          {/* Article Image if present */}
          {article.imageUrl && (
            <div className="rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800">
              <img
                src={article.imageUrl}
                alt={article.title}
                className="w-full max-h-64 object-cover"
              />
              {article.imageCaption && (
                <p className="text-xs text-stone-500 dark:text-stone-400 p-2 italic">
                  {article.imageCaption}
                </p>
              )}
            </div>
          )}

          {/* Body Paragraphs */}
          <div className={`space-y-4 text-stone-700 dark:text-stone-300 ${fontClasses[fontSize]}`}>
            {article.content.map((p, idx) => (
              <p key={idx} className={idx === 0 ? 'first-letter:font-serif first-letter:text-3xl first-letter:font-bold first-letter:float-left first-letter:mr-2 first-letter:leading-none' : ''}>
                {p}
              </p>
            ))}
          </div>
        </div>

        {/* Modal Footer */}
        <div className="px-6 py-3 border-t border-stone-200 dark:border-stone-800 bg-stone-50 dark:bg-stone-950 flex items-center justify-between">
          <span className="text-xs text-stone-500 dark:text-stone-400">
            അച്ചടി പത്രത്തിൽ നിന്ന് ഡിജിറ്റലായി വേർതിരിച്ചെടുത്തത്
          </span>

          <Link
            href={`/article/${article.id}`}
            onClick={onClose}
            className="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-red-700 hover:bg-red-800 text-white transition"
          >
            <span>പൂർണ്ണ വിവരണം വായിക്കുക</span>
            <ArrowUpRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>
    </div>
  );
}
