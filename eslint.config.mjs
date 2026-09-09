import { defineConfig, globalIgnores } from "eslint/config";
import nextVitals from "eslint-config-next/core-web-vitals";
import nextTypeScript from "eslint-config-next/typescript";

export default defineConfig([
  ...nextVitals,
  ...nextTypeScript,
  {
    rules: {
      "@typescript-eslint/no-explicit-any": "error",
      "@typescript-eslint/consistent-type-imports": "error",
      "@next/next/no-html-link-for-pages": "off",
      "@next/next/no-img-element": "off"
    }
  },
  globalIgnores([
    ".next/**",
    "node_modules/**",
    "next-env.d.ts"
  ])
]);
