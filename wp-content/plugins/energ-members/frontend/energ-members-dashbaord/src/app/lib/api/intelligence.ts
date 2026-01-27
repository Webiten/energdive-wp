import { api } from "./client";

export type IntelligenceArticle = {
  id: number;
  title: string;
  url: string;
};

export type IntelligenceItem = {
  id: number;
  title: string;
  excerpt: string;
  read_time: string;
  sector: string;
  articles: IntelligenceArticle[];
};

export const getIntelligence = async (): Promise<IntelligenceItem[]> => {
  const res = await api.get("/energ/v1/intelligence");
  return res.data;
};
