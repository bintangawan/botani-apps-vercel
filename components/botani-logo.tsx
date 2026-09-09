import Image from "next/image";
import { cn } from "@/lib/utils";

type BotaniLogoProps = {
  className?: string;
  decorative?: boolean;
  priority?: boolean;
};

export function BotaniLogo({
  className,
  decorative = false,
  priority = false,
}: BotaniLogoProps) {
  return (
    <Image
      src="/images/logo-botani.png"
      alt={decorative ? "" : "Logo Botani Phanerogamae"}
      width={500}
      height={500}
      sizes="96px"
      className={cn("object-contain", className)}
      aria-hidden={decorative || undefined}
      loading={priority ? "eager" : "lazy"}
      decoding="async"
    />
  );
}
