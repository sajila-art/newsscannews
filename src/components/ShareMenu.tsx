'use client';

import React, { useState, useRef, useEffect } from 'react';
import { Share2, Check, Copy, ExternalLink } from 'lucide-react';

interface ShareMenuProps {
  title: string;
  url?: string;
  buttonClassName?: string;
}

export default function ShareMenu({ title, url, buttonClassName }: ShareMenuProps) {
  const [open, setOpen] = useState(false);
  const [copied, setCopied] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!open) return;
    const handleOutside = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    };
    const handleEsc = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setOpen(false);
    };
    document.addEventListener('mousedown', handleOutside);
    document.addEventListener('keydown', handleEsc);
    return () => {
      document.removeEventListener('mousedown', handleOutside);
      document.removeEventListener('keydown', handleEsc);
    };
  }, [open]);

  const getShareUrl = () => {
    if (url) {
      if (url.startsWith('http://') || url.startsWith('https://')) return url;
      if (typeof window !== 'undefined') return `${window.location.origin}${url.startsWith('/') ? '' : '/'}${url}`;
    }
    return typeof window !== 'undefined' ? window.location.href : '';
  };

  const openShareLink = (href: string) => {
    window.open(href, '_blank', 'noopener,noreferrer');
    setOpen(false);
  };

  const handleCopy = async () => {
    const shareUrl = getShareUrl();
    if (typeof navigator !== 'undefined' && navigator.clipboard && shareUrl) {
      await navigator.clipboard.writeText(shareUrl);
      setCopied(true);
      setTimeout(() => {
        setCopied(false);
        setOpen(false);
      }, 1200);
    }
  };

  const handleNativeShare = async () => {
    const shareUrl = getShareUrl();
    if (typeof navigator !== 'undefined' && typeof navigator.share === 'function') {
      try {
        await navigator.share({ title, url: shareUrl });
        setOpen(false);
      } catch {
        // User cancelled native share
      }
    }
  };

  const shareUrl = getShareUrl();
  const encodedUrl = encodeURIComponent(shareUrl);
  const encodedTitle = encodeURIComponent(title);

  const channels = [
    {
      name: 'WhatsApp',
      color: 'text-emerald-600 dark:text-emerald-400',
      href: `https://wa.me/?text=${encodeURIComponent(`${title}\n${shareUrl}`)}`,
    },
    {
      name: 'Facebook',
      color: 'text-blue-600 dark:text-blue-400',
      href: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
    },
    {
      name: 'X (Twitter)',
      color: 'text-stone-900 dark:text-stone-100',
      href: `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`,
    },
    {
      name: 'Telegram',
      color: 'text-sky-600 dark:text-sky-400',
      href: `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`,
    },
  ];

  const hasNativeShare = typeof navigator !== 'undefined' && typeof navigator.share === 'function';

  return (
    <div ref={containerRef} className="relative inline-block">
      <button
        type="button"
        onClick={() => setOpen((prev) => !prev)}
        className={
          buttonClassName ||
          'p-2 rounded-lg bg-stone-200 dark:bg-stone-800 text-stone-700 dark:text-stone-200 hover:bg-stone-300 transition'
        }
        title="ഷെയർ ചെയ്യുക"
        aria-expanded={open}
        aria-haspopup="menu"
      >
        {copied ? <Check className="w-4 h-4 text-emerald-600" /> : <Share2 className="w-4 h-4" />}
      </button>

      {open && (
        <div
          role="menu"
          className="absolute right-0 mt-2 w-48 rounded-xl bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-700 shadow-xl py-1.5 z-50 text-xs"
        >
          <div className="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-400">
            ഷെയർ ചെയ്യുക
          </div>
          {channels.map((ch) => (
            <button
              key={ch.name}
              type="button"
              role="menuitem"
              onClick={() => openShareLink(ch.href)}
              className="w-full px-3 py-2 flex items-center justify-between text-left hover:bg-stone-100 dark:hover:bg-stone-800 transition"
            >
              <span className={`font-semibold ${ch.color}`}>{ch.name}</span>
              <ExternalLink className="w-3.5 h-3.5 text-stone-400" />
            </button>
          ))}
          <div className="my-1 border-t border-stone-200 dark:border-stone-800" />
          <button
            type="button"
            role="menuitem"
            onClick={handleCopy}
            className="w-full px-3 py-2 flex items-center justify-between text-left text-stone-700 dark:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-800 transition"
          >
            <span className="font-medium">{copied ? 'ലിങ്ക് കോപ്പി ചെയ്തു!' : 'ലിങ്ക് കോപ്പി ചെയ്യുക'}</span>
            {copied ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5 text-stone-400" />}
          </button>
          {hasNativeShare && (
            <button
              type="button"
              role="menuitem"
              onClick={handleNativeShare}
              className="w-full px-3 py-2 flex items-center justify-between text-left text-stone-700 dark:text-stone-200 hover:bg-stone-100 dark:hover:bg-stone-800 transition"
            >
              <span className="font-medium">മറ്റ് ആപ്പുകൾ...</span>
              <Share2 className="w-3.5 h-3.5 text-stone-400" />
            </button>
          )}
        </div>
      )}
    </div>
  );
}
