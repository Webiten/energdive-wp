import React, { useMemo, useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { TrendingUp, FileText } from "lucide-react";
import { useMe, getMeDisplayName } from "../hooks/useMe";
import { useTrendingNews } from "../hooks/useTrendingNews";

type Article = {
  id: number;
  title: string;
  url: string;
};

type IntelligenceGroup = {
  sector?: string;
  articles?: Article[];
};

export function DashboardHome() {
  const { me, loading } = useMe();

  // ====== INTELLIGENCE (SAME WAY AS IntelligenceSection.tsx) ======
  const [groups, setGroups] = useState<IntelligenceGroup[]>([]);
  const [feedLoading, setFeedLoading] = useState(true);

  useEffect(() => {
    const token =
      localStorage.getItem("access_token") ||
      localStorage.getItem("auth_token");

    fetch("/wp-json/energ/v1/intelligence", {
      headers: {
        Authorization: token ? `Bearer ${token}` : "",
      },
    })
      .then((res) => {
        if (!res.ok) throw new Error("Unauthorized / API failed");
        return res.json();
      })
      .then((data) => {
        setGroups(Array.isArray(data) ? data : []);
      })
      .catch((err) => {
        console.error("Dashboard intelligence fetch failed:", err);
        setGroups([]);
      })
      .finally(() => setFeedLoading(false));
  }, []);

  // ====== TRENDING (keep your hook) ======
  const {
    data: trendingNews = [],
    loading: trendingLoading,
  } = useTrendingNews();

  const welcomeName = useMemo(() => {
    return loading ? "…" : getMeDisplayName(me);
  }, [loading, me]);

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8 space-y-8">
        {/* Welcome */}
        <div>
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">
            Welcome back, {welcomeName}
          </h1>
          <p className="text-gray-600">
            Your intelligence hub for energy industry insights
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* ====== INTELLIGENCE FEED (DASHBOARD STYLE) ====== */}
          <div className="lg:col-span-2 space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span>Intelligence Feed</span>
                  <Badge variant="outline">Latest Updates</Badge>
                </CardTitle>
              </CardHeader>

              <CardContent className="space-y-6">
                {feedLoading && (
                  <p className="text-sm text-gray-500">
                    Loading intelligence...
                  </p>
                )}

                {!feedLoading && groups.length === 0 && (
                  <p className="text-sm text-gray-500">
                    No intelligence available yet.
                  </p>
                )}

                {groups.map((group, idx) => {
                  const articles = Array.isArray(group.articles)
                    ? group.articles
                    : [];

                  return (
                    <div key={idx} className="space-y-4">
                      <Badge variant="outline" className="text-xs">
                        {group.sector || "Intelligence"}
                      </Badge>

                      {articles.map((article) => (
                        <div
                          key={article.id}
                          className="pb-6 border-b last:border-b-0 last:pb-0"
                        >
                          <h3 className="text-lg font-semibold text-gray-900 hover:text-emerald-600 cursor-pointer">
                            {article.title}
                          </h3>

                          <a
                            href={article.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-sm text-emerald-600 hover:underline"
                          >
                            Read article →
                          </a>
                        </div>
                      ))}
                    </div>
                  );
                })}
              </CardContent>
            </Card>
          </div>

          {/* ====== TRENDING SIDEBAR ====== */}
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <TrendingUp className="w-5 h-5 text-emerald-600" />
                  Trending This Week
                </CardTitle>
              </CardHeader>

              <CardContent className="space-y-4">
                {trendingLoading && (
                  <p className="text-sm text-gray-500">
                    Loading trending news...
                  </p>
                )}

                {trendingNews.map((item: any) => (
                  <div
                    key={item.id}
                    className="pb-4 border-b last:border-b-0 last:pb-0"
                  >
                    <h4 className="text-sm font-medium text-gray-900 mb-2 hover:text-emerald-600 cursor-pointer">
                      {item.title}
                    </h4>

                    <div className="flex items-center justify-between text-xs text-gray-500">
                      <Badge variant="outline">
                        {item.category || "General"}
                      </Badge>

                      {item.views ? (
                        <span>{item.views} views</span>
                      ) : (
                        <span>Trending</span>
                      )}
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Placeholder */}
            <Card className="bg-gradient-to-br from-emerald-50 to-emerald-100 border-emerald-200">
              <CardContent className="pt-6 text-center">
                <div className="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <FileText className="w-6 h-6 text-white" />
                </div>
                <h4 className="font-semibold text-gray-900 mb-2">
                  More Features Coming
                </h4>
                <p className="text-sm text-gray-600">
                  Data tools, AI insights, and executive lounges will be added soon.
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}
