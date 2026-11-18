'use client';

import { useState, useRef, useEffect, useCallback, useMemo } from 'react';

export function splitMalayalamSentences(text: string): string[] {
  if (!text) return [];
  const rawParts = text
    .split(/(?<=[.!?\n।])\s+|\n+/u)
    .map((s) => s.trim())
    .filter(Boolean);

  const chunks: string[] = [];
  for (const part of rawParts) {
    if (part.length <= 180) {
      chunks.push(part);
    } else {
      // Split long sentences at commas or spaces within 180 chars
      let remaining = part;
      while (remaining.length > 180) {
        let cut = remaining.lastIndexOf(' ', 180);
        if (cut < 40) cut = 180;
        chunks.push(remaining.slice(0, cut).trim());
        remaining = remaining.slice(cut).trim();
      }
      if (remaining) chunks.push(remaining);
    }
  }
  return chunks;
}

function formatSeconds(secs: number): string {
  const m = Math.floor(secs / 60);
  const s = Math.floor(secs % 60);
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
}

interface UseMalayalamSpeechOptions {
  text: string;
  audioUrl?: string | null;
}

export function useMalayalamSpeech({ text, audioUrl }: UseMalayalamSpeechOptions) {
  const sentences = useMemo(() => splitMalayalamSentences(text), [text]);
  const [isPlaying, setIsPlaying] = useState(false);
  const [activeIndex, setActiveIndex] = useState<number>(-1);
  const [progressPct, setProgressPct] = useState(0);
  const [elapsedLabel, setElapsedLabel] = useState('00:00');

  const audioRef = useRef<HTMLAudioElement | null>(null);
  const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const cancelledRef = useRef(false);

  const stop = useCallback(() => {
    cancelledRef.current = true;
    setIsPlaying(false);
    setActiveIndex(-1);
    setProgressPct(0);
    setElapsedLabel('00:00');
    if (timerRef.current) {
      clearInterval(timerRef.current);
      timerRef.current = null;
    }
    if (audioRef.current) {
      audioRef.current.pause();
      audioRef.current.onended = null;
      audioRef.current.onerror = null;
      audioRef.current.ontimeupdate = null;
      audioRef.current = null;
    }
    if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
      window.speechSynthesis.cancel();
    }
  }, []);

  useEffect(() => {
    return () => stop();
  }, [stop]);

  const speakChunkWithBrowserFallback = useCallback(
    (index: number) => {
      if (cancelledRef.current || index >= sentences.length) {
        stop();
        return;
      }
      if (typeof window === 'undefined' || !('speechSynthesis' in window)) {
        stop();
        return;
      }

      setActiveIndex(index);
      setProgressPct(((index + 0.5) / Math.max(1, sentences.length)) * 100);

      const utterance = new SpeechSynthesisUtterance(sentences[index]);
      utterance.lang = 'ml-IN';
      utterance.rate = 0.95;
      utterance.onend = () => {
        if (!cancelledRef.current) {
          speakChunkWithBrowserFallback(index + 1);
        }
      };
      utterance.onerror = () => {
        stop();
      };
      window.speechSynthesis.speak(utterance);
    },
    [sentences, stop]
  );

  const playChunkAt = useCallback(
    (index: number) => {
      if (cancelledRef.current || index >= sentences.length) {
        stop();
        return;
      }

      setActiveIndex(index);
      const pct = (index / Math.max(1, sentences.length)) * 100;
      setProgressPct(pct);

      const ttsSrc = `/api/tts?lang=ml&q=${encodeURIComponent(sentences[index])}`;
      const audio = new Audio(ttsSrc);
      audioRef.current = audio;

      audio.onended = () => {
        if (!cancelledRef.current) {
          playChunkAt(index + 1);
        }
      };

      audio.onerror = () => {
        if (!cancelledRef.current) {
          speakChunkWithBrowserFallback(index);
        }
      };

      audio.play().catch(() => {
        if (!cancelledRef.current) {
          speakChunkWithBrowserFallback(index);
        }
      });
    },
    [sentences, speakChunkWithBrowserFallback, stop]
  );

  const play = useCallback(
    (startIndex = 0) => {
      stop();
      cancelledRef.current = false;

      if (audioUrl && startIndex === 0) {
        const audio = new Audio(audioUrl);
        audioRef.current = audio;
        setIsPlaying(true);
        setActiveIndex(0);

        audio.ontimeupdate = () => {
          if (audio.duration > 0) {
            const ratio = audio.currentTime / audio.duration;
            setProgressPct(ratio * 100);
            setElapsedLabel(formatSeconds(audio.currentTime));
            if (sentences.length > 0) {
              const idx = Math.min(sentences.length - 1, Math.floor(ratio * sentences.length));
              setActiveIndex(idx);
            }
          }
        };

        audio.onended = () => stop();
        audio.onerror = () => {
          // If saved MP3 fails, fall back seamlessly to chunked Malayalam TTS
          if (!cancelledRef.current && sentences.length > 0) {
            playChunkAt(0);
          } else {
            stop();
          }
        };

        audio.play().catch(() => {
          if (!cancelledRef.current && sentences.length > 0) {
            playChunkAt(0);
          }
        });
        return;
      }

      if (sentences.length === 0) return;

      setIsPlaying(true);
      let secs = 0;
      timerRef.current = setInterval(() => {
        secs += 1;
        setElapsedLabel(formatSeconds(secs));
      }, 1000);

      playChunkAt(startIndex);
    },
    [audioUrl, playChunkAt, sentences.length, stop]
  );

  const toggle = useCallback(() => {
    if (isPlaying) {
      stop();
    } else {
      play(0);
    }
  }, [isPlaying, play, stop]);

  return {
    sentences,
    isPlaying,
    activeIndex,
    progressPct,
    elapsedLabel,
    play,
    stop,
    toggle,
  };
}
