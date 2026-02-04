import React, { useMemo } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { TrendingUp, Users, FileText, MessageSquare } from "lucide-react";
import { Avatar, AvatarFallback } from "./ui/avatar";
import { useMe, getMeDisplayName } from "../hooks/useMe";

import { useIntelligence } from "../hooks/useIntelligence";

import { useTrendingNews } from "../hooks/useTrendingNews";

// const mockCommunity = [
//   {
//     contributor: "Dr. Emily Watson",
//     role: "Energy Policy Analyst",
//     contribution: "Shared insights on offshore wind farm regulations",
//     timestamp: "2 hours ago",
//     discussionTitle: "Grid flexibility: How are different markets solving intermittency?",
//     replies: 12,
//   },
//   {
//     contributor: "Michael Zhang",
//     role: "Senior Consultant",
//     contribution: "Published analysis on hydrogen infrastructure development",
//     timestamp: "5 hours ago",
//     discussionTitle: "Green hydrogen adoption timeline in heavy industry",
//     replies: 8,
//   },
//   {
//     contributor: "Lisa Anderson",
//     role: "Industry Practitioner",
//     contribution: "Comment on nuclear energy investment trends",
//     timestamp: "1 day ago",
//     discussionTitle: "Nuclear vs Renewables: Can we have both?",
//     replies: 15,
//   },
// ];

// const activeDiscussions = [
//   {
//     title: "What's the realistic timeline for green hydrogen adoption?",
//     category: "Technology",
//     replies: 128,
//     participants: 45,
//     trending: true,
//   },
//   {
//     title: "Carbon offset verification: Current challenges",
//     category: "Policy",
//     replies: 91,
//     participants: 32,
//     trending: false,
//   },
//   {
//     title: "Grid modernization best practices",
//     category: "Infrastructure",
//     replies: 145,
//     participants: 56,
//     trending: true,
//   },
//   {
//     title: "EV charging infrastructure business models",
//     category: "Market",
//     replies: 112,
//     participants: 38,
//     trending: false,
//   },
// ];

export function DashboardHome() {
  const { me, loading } = useMe();

  // ✅ FIX: useIntelligence hook ko actually use karo
  const {
    data: intelligenceFeed = [],
    loading: intelligenceLoading,
  } = useIntelligence();

  // ✅ FIX: trending hook ko use karo
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

        {/* Welcome Section */}
        <div>
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">
            Welcome back, {welcomeName}
          </h1>
          <p className="text-gray-600">
            Your intelligence hub for energy industry insights
          </p>
        </div>

        {/* Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">

          {/* Intelligence Feed */}
          <div className="lg:col-span-2 space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span>Intelligence Feed</span>
                  <Badge variant="outline">Latest Updates</Badge>
                </CardTitle>
              </CardHeader>

              <CardContent className="space-y-6">
                {intelligenceLoading && (
                  <p className="text-sm text-gray-500">Loading feed...</p>
                )}

                {intelligenceFeed.length === 0 && !intelligenceLoading && (
                  <p className="text-sm text-gray-500">
                    No intelligence available yet.
                  </p>
                )}

                {intelligenceFeed.map((article: any) => (
                  <div
                    key={article.id}
                    className="pb-6 border-b last:border-b-0 last:pb-0"
                  >
                    <div className="flex items-start gap-3 mb-3">
                      <Badge variant="secondary" className="text-xs">
                        {article.category || "General"}
                      </Badge>
                      <span className="text-xs text-gray-500">
                        {article.readTime || ""}
                      </span>
                    </div>

                    <h3 className="text-lg font-semibold text-gray-900 hover:text-emerald-600 cursor-pointer">
                      {article.title}
                    </h3>

                    <p className="text-sm text-gray-600 mb-3">
                      {article.excerpt}
                    </p>

                    <div className="flex items-center justify-between text-sm text-gray-500">
                      <span>{article.author}</span>
                      <span>{article.date}</span>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>
          </div>

          {/* Sidebar - Trending */}
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

            {/* Placeholder Card */}
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

