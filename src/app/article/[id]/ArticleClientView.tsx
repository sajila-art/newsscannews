'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import {
  Clock,
  BookOpen,
  Volume2,
  VolumeX,
  Printer,
  ChevronLeft,
  ArrowUpRight,
  MessageSquare,
  ThumbsUp,
} from 'lucide-react';
import { Article } from '../../../types/news';
import { useMalayalamSpeech } from '../../../lib/useMalayalamSpeech';
import ShareMenu from '../../../components/ShareMenu';

interface ArticleClientViewProps {
  article: Article;
  relatedArticles: Article[];
}

export default function ArticleClientView({
  article,
  relatedArticles,
}: ArticleClientViewProps) {
  const [fontSize, setFontSize] = useState<'normal' | 'large' | 'xlarge'>('normal');
  const textToSpeak = `${article.title}. ${article.subtitle || ''}. ${article.author.name} നൽകുന്ന റിപ്പോർട്ട്. ${article.content.join(' ')}`;
  const { isPlaying: isSpeaking, toggle: handleSpeech } = useMalayalamSpeech({ text: textToSpeak });
  const [commentText, setCommentText] = useState('');
  const [comments, setComments] = useState([
    {
      id: 'c1',
      name: 'അഡ്വ. സുരേഷ് കുമാർ',
      date: '2 മണിക്കൂർ മുമ്പ്',
      text: 'വളരെ പ്രയോജനകരമായ വാർത്ത. സാധാരണക്കാർക്ക് ഇത് വലിയൊരു ആശ്വാസമാകും.',
      likes: 18,
    },
    {
      id: 'c2',
      name: 'രമ്യ മോഹൻ',
      date: '45 മിനിറ്റ് മുമ്പ്',
      text: 'ഇന്നത്തെ ഇ-പേപ്പർ സ്കാൻ പതിപ്പിൽ ഒന്നാം പേജിൽ തന്നെ ഈ വാർത്ത വായിച്ചിരുന്നു. അഭിനന്ദനങ്ങൾ!',
      likes: 7,
    },
  ]);

  const fontClasses = {
    normal: 'text-base sm:text-lg leading-relaxed',
    large: 'text-lg sm:text-xl leading-relaxed',
    xlarge: 'text-xl sm:text-2xl leading-relaxed',
  };

  const handlePrint = () => {
    window.print();
  };

  const handleAddComment = (e: React.FormEvent) => {
    e.preventDefault();
    if (commentText.trim()) {
      setComments([
        {
          id: `c-${Date.now()}`,
          name: 'വായനക്കാരൻ',
          date: 'ഇപ്പോൾ',
          text: commentText.trim(),
          likes: 0,
        },
        ...comments,
      ]);
      setCommentText('');
    }
  };

  return (
    <article className="max-w-4xl mx-auto px-4 py-8 font-sans">
      {/* Top Breadcrumb & Scanned Link */}
      <div className="flex items-center justify-between text-xs text-stone-500 dark:text-stone-400 mb-6 border-b border-stone-200 dark:border-stone-800 pb-3">
        <div className="flex items-center gap-2">
          <Link href="/" className="hover:text-red-600 flex items-center gap-1 font-medium">
            <ChevronLeft className="w-3.5 h-3.5" />
            ഹോം
          </Link>
          <span>/</span>
          <Link href={`/categories/${article.categorySlug || 'all'}`} className="hover:text-red-600 font-medium">
            {article.category}
          </Link>
        </div>

        {article.scannedPageRef && (
          <Link
            href={`/scan-news?page=${article.scannedPageRef.pageNumber}`}
            className="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 font-semibold rounded-full border border-red-200 dark:border-red-900 hover:bg-red-100 transition"
          >
            <BookOpen className="w-3.5 h-3.5" />
            <span>N-News e-paper പേജ് {article.scannedPageRef.pageNumber} കാണുക</span>
          </Link>
        )}
      </div>

      {/* Category badge & headline */}
      <div className="mb-6">
        <span className="px-2.5 py-1 text-xs font-bold bg-red-600 text-white rounded inline-block mb-3">
          {article.category}
        </span>
        <h1 className="font-serif text-2xl sm:text-4xl md:text-5xl font-black text-stone-900 dark:text-stone-100 leading-snug">
          {article.title}
        </h1>
        {article.subtitle && (
          <p className="font-serif italic text-base sm:text-lg text-stone-600 dark:text-stone-300 mt-3 leading-relaxed">
            {article.subtitle}
          </p>
        )}
      </div>

      {/* Author Byline & Article Action Bar */}
      <div className="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stone-200 dark:border-stone-800 mb-8 bg-stone-50/50 dark:bg-stone-900/30 px-3 rounded-xl">
        {/* Byline */}
        <div className="flex items-center gap-3">
          {article.author.avatar && (
            <img
              src={article.author.avatar}
              alt={article.author.name}
              className="w-11 h-11 rounded-full object-cover ring-2 ring-red-600/20"
            />
          )}
          <div>
            <p className="text-sm font-bold text-stone-900 dark:text-stone-100">
              {article.author.name}
            </p>
            <p className="text-xs text-stone-500 dark:text-stone-400">
              {article.author.role} • {article.publishedAt}
            </p>
          </div>
        </div>

        {/* Actions: Audio, Font Size, Print, Share */}
        <div className="flex items-center gap-2">
          {/* Audio reader button */}
          <button
            onClick={handleSpeech}
            className={`flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition ${
              isSpeaking
                ? 'bg-red-600 text-white animate-pulse'
                : 'bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-200 hover:bg-stone-300'
            }`}
            title="വാർത്ത ഓഡിയോ ആയി കേൾക്കാം"
          >
            {isSpeaking ? <VolumeX className="w-3.5 h-3.5" /> : <Volume2 className="w-3.5 h-3.5" />}
            <span>{isSpeaking ? 'നിർത്തുക' : 'കേൾക്കുക'}</span>
          </button>

          {/* Font size switcher */}
          <div className="flex items-center bg-stone-200 dark:bg-stone-800 rounded-lg p-0.5 text-xs">
            <button
              onClick={() => setFontSize('normal')}
              className={`px-2 py-1 rounded font-bold ${
                fontSize === 'normal' ? 'bg-white dark:bg-stone-700 shadow-xs' : 'text-stone-600 dark:text-stone-400'
              }`}
            >
              A
            </button>
            <button
              onClick={() => setFontSize('large')}
              className={`px-2 py-1 rounded font-bold text-sm ${
                fontSize === 'large' ? 'bg-white dark:bg-stone-700 shadow-xs' : 'text-stone-600 dark:text-stone-400'
              }`}
            >
              A+
            </button>
            <button
              onClick={() => setFontSize('xlarge')}
              className={`px-2 py-1 rounded font-bold text-base ${
                fontSize === 'xlarge' ? 'bg-white dark:bg-stone-700 shadow-xs' : 'text-stone-600 dark:text-stone-400'
              }`}
            >
              A++
            </button>
          </div>

          {/* Print */}
          <button
            onClick={handlePrint}
            className="p-2 rounded-lg bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-200 hover:bg-stone-300 transition"
            title="വാർത്ത പ്രിന്റ് ചെയ്യുക"
          >
            <Printer className="w-4 h-4" />
          </button>

          {/* Share */}
          <ShareMenu title={article.title} />
        </div>
      </div>

      {/* Featured Photo */}
      <div className="mb-8 rounded-2xl overflow-hidden shadow-sm bg-stone-100 dark:bg-stone-900">
        <img
          src={article.imageUrl}
          alt={article.title}
          className="w-full max-h-[480px] object-cover"
        />
        {article.imageCaption && (
          <div className="p-3 bg-stone-100 dark:bg-stone-900 border-t border-stone-200 dark:border-stone-800 text-xs italic text-stone-600 dark:text-stone-400">
            {article.imageCaption}
          </div>
        )}
      </div>

      {/* Main Article Prose */}
      <div className={`space-y-6 text-stone-800 dark:text-stone-200 ${fontClasses[fontSize]}`}>
        {article.content.map((para, index) => (
          <p
            key={index}
            className={
              index === 0
                ? 'newspaper-dropcap first-letter:text-stone-950 dark:first-letter:text-stone-50'
                : ''
            }
          >
            {para}
          </p>
        ))}
      </div>

      {/* Scanned Newspaper Page Callout Box */}
      {article.scannedPageRef && (
        <div className="my-10 p-5 rounded-2xl bg-stone-100 dark:bg-stone-900 border border-stone-300 dark:border-stone-700 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-3">
            <div className="p-3 bg-red-600 text-white rounded-xl">
              <BookOpen className="w-6 h-6" />
            </div>
            <div>
              <h4 className="font-serif text-base font-bold text-stone-900 dark:text-stone-100">
                അച്ചടി പത്രത്തിലെ സ്കാൻ പേജ്
              </h4>
              <p className="text-xs text-stone-600 dark:text-stone-400">
                ഇന്നത്തെ {article.scannedPageRef.editionName}-ന്റെ {article.scannedPageRef.pageNumber}-ാം പേജിൽ ഈ വാർത്ത വായിക്കാം.
              </p>
            </div>
          </div>
          <Link
            href={`/scan-news?page=${article.scannedPageRef.pageNumber}`}
            className="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg bg-stone-900 dark:bg-stone-100 text-stone-100 dark:text-stone-900 hover:bg-red-700 dark:hover:bg-red-600 dark:hover:text-white transition whitespace-nowrap"
          >
            <span>സ്കാൻ പേജ് തുറക്കുക</span>
            <ArrowUpRight className="w-3.5 h-3.5" />
          </Link>
        </div>
      )}

      {/* Tags */}
      <div className="pt-6 border-t border-stone-200 dark:border-stone-800 flex flex-wrap items-center gap-2 mb-10">
        <span className="text-xs font-bold text-stone-400 mr-2">വിഷയങ്ങൾ:</span>
        {article.tags.map((tag) => (
          <span
            key={tag}
            className="text-xs px-3 py-1 rounded-full bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 font-medium"
          >
            #{tag}
          </span>
        ))}
      </div>

      {/* Reader Comments Section */}
      <div className="border-t border-stone-200 dark:border-stone-800 pt-8 mb-12">
        <h3 className="font-serif text-xl sm:text-2xl font-bold text-stone-900 dark:text-stone-100 mb-6 flex items-center gap-2">
          <MessageSquare className="w-5 h-5 text-red-600" />
          <span>വായനക്കാരുടെ അഭിപ്രായങ്ങൾ ({comments.length})</span>
        </h3>

        {/* Comment form */}
        <form onSubmit={handleAddComment} className="mb-6 space-y-3">
          <textarea
            value={commentText}
            onChange={(e) => setCommentText(e.target.value)}
            placeholder="ഈ വാർത്തയെക്കുറിച്ചുള്ള നിങ്ങളുടെ അഭിപ്രായം ഇവിടെ രേഖപ്പെടുത്താം..."
            rows={3}
            className="w-full text-sm p-3 rounded-xl border border-stone-300 dark:border-stone-700 bg-white dark:bg-stone-900 text-stone-900 dark:text-stone-100 focus:outline-none focus:ring-1 focus:ring-red-600"
          />
          <div className="flex justify-end">
            <button
              type="submit"
              className="px-4 py-2 text-xs font-bold rounded-lg bg-red-700 hover:bg-red-800 text-white transition"
            >
              അഭിപ്രായം രേഖപ്പെടുത്തുക
            </button>
          </div>
        </form>

        {/* Comments list */}
        <div className="space-y-4">
          {comments.map((c) => (
            <div
              key={c.id}
              className="p-4 rounded-xl bg-stone-50 dark:bg-stone-900/50 border border-stone-200 dark:border-stone-800"
            >
              <div className="flex items-center justify-between mb-2">
                <span className="font-bold text-xs text-stone-900 dark:text-stone-100">
                  {c.name}
                </span>
                <span className="text-[11px] text-stone-400">{c.date}</span>
              </div>
              <p className="text-xs sm:text-sm text-stone-700 dark:text-stone-300 mb-2">
                {c.text}
              </p>
              <div className="flex items-center gap-1 text-[11px] text-stone-400">
                <ThumbsUp className="w-3 h-3" />
                <span>{c.likes} പേർ യോജിക്കുന്നു</span>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Related Stories */}
      {relatedArticles.length > 0 && (
        <div className="border-t-2 border-stone-900 dark:border-stone-100 pt-8">
          <h3 className="font-serif text-lg font-bold text-stone-900 dark:text-stone-100 mb-6">
            കൂടുതൽ വാർത്തകൾ
          </h3>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
            {relatedArticles.map((rel) => (
              <Link
                key={rel.id}
                href={`/article/${rel.id}`}
                className="group flex flex-col justify-between"
              >
                <div className="aspect-[16/10] rounded-xl overflow-hidden bg-stone-100 dark:bg-stone-800 mb-2">
                  <img
                    src={rel.imageUrl}
                    alt={rel.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                </div>
                <div>
                  <span className="text-[10px] font-bold text-red-600">
                    {rel.category}
                  </span>
                  <h4 className="font-serif text-sm font-bold text-stone-900 dark:text-stone-100 group-hover:text-red-600 transition-colors line-clamp-2 leading-snug mt-0.5">
                    {rel.title}
                  </h4>
                </div>
              </Link>
            ))}
          </div>
        </div>
      )}
    </article>
  );
}
