import { useEffect, useRef } from "react";
import { useSessionStore } from "../stores/sessionStore";

type Options = {
  timeoutMs: number;
  onExpire?: () => void;
} | null;

export function useSessionTimeout(options: Options) {
  const timerRef = useRef<number | null>(null);
  const expire = useSessionStore((s) => s.expire);

  useEffect(() => {
    // ❌ No session → no timer
    if (!options) {
      if (timerRef.current) {
        clearTimeout(timerRef.current);
        timerRef.current = null;
      }
      return;
    }

    const { timeoutMs, onExpire } = options;

    // 🧹 clear old timer
    if (timerRef.current) {
      clearTimeout(timerRef.current);
    }

    console.log("🕒 Session timer started:", timeoutMs);

    timerRef.current = window.setTimeout(() => {
      console.log("⛔ Session expired");
      expire();
      onExpire?.();
    }, timeoutMs);

    return () => {
      if (timerRef.current) {
        clearTimeout(timerRef.current);
      }
    };
  }, [options, expire]);
}
