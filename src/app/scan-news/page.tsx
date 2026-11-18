import React, { Suspense } from 'react';
import ScanNewsViewer from '../../components/ScanNewsViewer';

export const metadata = {
  title: 'N-News e-paper Reader | N-NEWS Chronicle',
  description: 'Interactive high-definition newspaper scan viewer with smart article clipping, zoom controls, and text-to-speech OCR reader.',
};

export default function ScanNewsPage() {
  return (
    <div className="flex-1 flex flex-col">
      <Suspense
        fallback={
          <div className="flex-1 flex items-center justify-center p-20 text-stone-400">
            Loading N-News e-paper Edition...
          </div>
        }
      >
        <ScanNewsViewer />
      </Suspense>
    </div>
  );
}

