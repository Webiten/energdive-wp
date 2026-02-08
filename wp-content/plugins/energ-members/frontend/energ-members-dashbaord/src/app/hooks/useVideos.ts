import { useEffect, useState } from "react";
import { VideoAPI } from "./api";

export interface VideoItem {
  id: number;
  title: string;
  date: string;
  thumbnail?: string | null;
  video_url?: string | null;
}

export function useVideos(limit = 6) {
  const [data, setData] = useState<VideoItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function fetchVideos() {
      setLoading(true);
      setError(null);

      try {
        const res = await VideoAPI.getVideos();
        setData((res.videos || []).slice(0, limit));
      } catch (err: any) {
        setError(err?.message || "Failed to load videos");
      } finally {
        setLoading(false);
      }
    }

    fetchVideos();
  }, [limit]);

  return { data, loading, error };
}
