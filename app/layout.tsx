import type { Metadata } from "next";
import { Google_Sans_Flex } from "next/font/google";
import type { ReactNode } from "react";
import { SpeedInsights } from "@vercel/speed-insights/next";
import { Providers } from "@/components/providers";
import "./globals.css";

const googleSansFlex = Google_Sans_Flex({
  subsets: ["latin"],
  weight: "variable",
  display: "swap",
  variable: "--font-google-sans-flex",
  fallback: ["Plus Jakarta Sans", "Outfit", "Segoe UI", "Arial", "sans-serif"],
});

export const metadata: Metadata = {
  title: { default: "Botani Phanerogamae", template: "%s — Botani Phanerogamae" },
  description: "Herbarium digital dan media pembelajaran tumbuhan berbiji Gymnospermae serta Angiospermae di Sumatera Utara.",
  applicationName: "Botani Phanerogamae",
};

export default function RootLayout({ children }: Readonly<{ children: ReactNode }>) {
  return (
    <html
      lang="id"
      className={`${googleSansFlex.variable} scroll-smooth`}
      data-scroll-behavior="smooth"
    >
      <body>
        <Providers>{children}</Providers>
        <SpeedInsights />
      </body>
    </html>
  );
}
