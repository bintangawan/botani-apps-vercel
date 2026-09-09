import type { NextConfig } from "next";

function getSupabaseImagePattern(): NonNullable<NextConfig["images"]>["remotePatterns"] {
  const value = process.env.NEXT_PUBLIC_SUPABASE_URL;
  const bucket = process.env.NEXT_PUBLIC_STORAGE_BUCKET;
  if (!value || !bucket) {
    return [];
  }

  const url = new URL(value);
  return [
    {
      protocol: url.protocol === "http:" ? "http" : "https",
      hostname: url.hostname,
      port: url.port,
      pathname: `/storage/v1/object/public/${bucket}/**`,
    },
  ];
}

const nextConfig: NextConfig = {
  cacheComponents: true,
  async redirects() {
    return ["plants", "modules", "quizzes"].map((resource) => ({
      source: `/manage/${resource}/create`,
      destination: `/manage/${resource}/new`,
      permanent: true,
    }));
  },
  images: {
    remotePatterns: getSupabaseImagePattern(),
  },
  poweredByHeader: false,
  reactStrictMode: true,
};

export default nextConfig;
