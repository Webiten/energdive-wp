import React, { useMemo } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { TrendingUp, Users, FileText, MessageSquare } from "lucide-react";
import { Avatar, AvatarFallback } from "./ui/avatar";
import { useMe, getMeDisplayName } from "../hooks/useMe";

const mockArticles = [
  {
    id: 1,
    title: "The Future of Renewable Energy: Policy Shifts in 2026",
    category: "Policy & Regulation",
    author: "Dr. Sarah Mitchell",
    date: "Jan 8, 2026",
    readTime: "8 min read",
    excerpt:
      "An in-depth analysis of the latest regulatory changes affecting renewable energy adoption across global markets.",
  },
  {
    id: 2,
    title: "Battery Storage Technologies: Market Analysis Q1 2026",
    category: "Technology & Innovation",
    author: "James Chen",
    date: "Jan 7, 2026",
    readTime: "12 min read",
    excerpt:
      "Comprehensive market overview of emerging battery storage solutions and their impact on grid infrastructure.",
  },
  {
    id: 3,
    title: "Carbon Markets: Trading Dynamics and Price Forecasts",
    category: "Market & Industry",
    author: "Maria Rodriguez",
    date: "Jan 6, 2026",
    readTime: "10 min read",
    excerpt:
      "Expert insights on carbon credit markets and projected pricing trends through 2027.",
  },
];

const mockTrending = [
  { title: "Global LNG Market Outlook 2026", views: "12.4K", category: "Market Analysis" },
  { title: "AI in Energy Grid Management", views: "8.9K", category: "Technology" },
  { title: "EU Green Deal: Latest Updates", views: "7.2K", category: "Policy" },
  { title: "Solar Panel Efficiency Breakthroughs", views: "6.8K", category: "Innovation" },
];

const mockCommunity = [
  {
    contributor: "Dr. Emily Watson",
    role: "Energy Policy Analyst",
    contribution: "Shared insights on offshore wind farm regulations",
    timestamp: "2 hours ago",
    discussionTitle: "Grid flexibility: How are different markets solving intermittency?",
    replies: 12,
  },
  {
    contributor: "Michael Zhang",
    role: "Senior Consultant",
    contribution: "Published analysis on hydrogen infrastructure development",
    timestamp: "5 hours ago",
    discussionTitle: "Green hydrogen adoption timeline in heavy industry",
    replies: 8,
  },
  {
    contributor: "Lisa Anderson",
    role: "Industry Practitioner",
    contribution: "Comment on nuclear energy investment trends",
    timestamp: "1 day ago",
    discussionTitle: "Nuclear vs Renewables: Can we have both?",
    replies: 15,
  },
];

const activeDiscussions = [
  {
    title: "What's the realistic timeline for green hydrogen adoption?",
    category: "Technology",
    replies: 128,
    participants: 45,
    trending: true,
  },
  {
    title: "Carbon offset verification: Current challenges",
    category: "Policy",
    replies: 91,
    participants: 32,
    trending: false,
  },
  {
    title: "Grid modernization best practices",
    category: "Infrastructure",
    replies: 145,
    participants: 56,
    trending: true,
  },
  {
    title: "EV charging infrastructure business models",
    category: "Market",
    replies: 112,
    participants: 38,
    trending: false,
  },
];

export function DashboardHome() {
  const { me, loading } = useMe();

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
          <p className="text-gray-600">Your intelligence hub for energy industry insights</p>
        </div>

        {/* Quick Stats */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <Card>
            <CardContent className="pt-6">
              <div className="flex items-center gap-3">
                <div className="p-2 bg-emerald-100 rounded-lg">
                  <FileText className="w-5 h-5 text-emerald-600" />
                </div>
                <div>
                  <p className="text-2xl font-semibold">124</p>
                  <p className="text-sm text-gray-600">Articles Published</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="flex items-center gap-3">
                <div className="p-2 bg-blue-100 rounded-lg">
                  <TrendingUp className="w-5 h-5 text-blue-600" />
                </div>
                <div>
                  <p className="text-2xl font-semibold">18</p>
                  <p className="text-sm text-gray-600">Trending Topics</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="flex items-center gap-3">
                <div className="p-2 bg-purple-100 rounded-lg">
                  <Users className="w-5 h-5 text-purple-600" />
                </div>
                <div>
                  <p className="text-2xl font-semibold">2.3K</p>
                  <p className="text-sm text-gray-600">Active Members</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="flex items-center gap-3">
                <div className="p-2 bg-orange-100 rounded-lg">
                  <MessageSquare className="w-5 h-5 text-orange-600" />
                </div>
                <div>
                  <p className="text-2xl font-semibold">89</p>
                  <p className="text-sm text-gray-600">Discussions</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Intelligence Feed - Takes 2 columns */}
          <div className="lg:col-span-2 space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span>Intelligence Feed</span>
                  <Badge variant="outline">Latest Updates</Badge>
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-6">
                {mockArticles.map((article) => (
                  <div key={article.id} className="pb-6 border-b last:border-b-0 last:pb-0">
                    <div className="flex items-start gap-3 mb-3">
                      <Badge variant="secondary" className="text-xs">
                        {article.category}
                      </Badge>
                      <span className="text-xs text-gray-500">{article.readTime}</span>
                    </div>
                    <h3 className="text-lg font-semibold text-gray-900 mb-2 hover:text-emerald-600 cursor-pointer">
                      {article.title}
                    </h3>
                    <p className="text-sm text-gray-600 mb-3">{article.excerpt}</p>
                    <div className="flex items-center justify-between text-sm text-gray-500">
                      <span>{article.author}</span>
                      <span>{article.date}</span>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Community Highlights */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span>Community Highlights</span>
                  <Badge variant="outline" className="text-xs">
                    Live Activity
                  </Badge>
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {mockCommunity.map((item, index) => (
                  <div key={index} className="flex gap-4 pb-4 border-b last:border-b-0 last:pb-0">
                    <Avatar className="w-10 h-10 flex-shrink-0">
                      <AvatarFallback className="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white">
                        {item.contributor.split(" ").map((n) => n[0]).join("")}
                      </AvatarFallback>
                    </Avatar>
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="font-medium text-gray-900">{item.contributor}</span>
                        <Badge variant="outline" className="text-xs">
                          {item.role}
                        </Badge>
                      </div>
                      <p className="text-sm text-gray-600 mb-1">{item.contribution}</p>
                      <p className="text-xs text-gray-900 font-medium mb-1 hover:text-emerald-600 cursor-pointer">
                        "{item.discussionTitle}"
                      </p>
                      <div className="flex items-center gap-2">
                        <span className="text-xs text-gray-500">{item.timestamp}</span>
                        <span className="text-xs text-gray-400">•</span>
                        <span className="text-xs text-gray-500">{item.replies} replies</span>
                      </div>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Active Discussions */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <MessageSquare className="w-5 h-5 text-emerald-600" />
                  Active Discussions
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {activeDiscussions.map((discussion, index) => (
                  <div key={index} className="pb-4 border-b last:border-b-0 last:pb-0">
                    <div className="flex items-start justify-between gap-2 mb-2">
                      <h4 className="text-sm font-medium text-gray-900 hover:text-emerald-600 cursor-pointer flex-1">
                        {discussion.title}
                      </h4>
                      {discussion.trending && (
                        <Badge className="bg-orange-600 text-xs flex-shrink-0">
                          <TrendingUp className="w-3 h-3 mr-1" />
                          Hot
                        </Badge>
                      )}
                    </div>
                    <div className="flex items-center gap-3 text-xs text-gray-500">
                      <Badge variant="outline" className="text-xs">
                        {discussion.category}
                      </Badge>
                      <span>{discussion.replies} replies</span>
                      <span>•</span>
                      <span>{discussion.participants} participants</span>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>
          </div>

          {/* Sidebar - Trending This Week */}
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <TrendingUp className="w-5 h-5 text-emerald-600" />
                  Trending This Week
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {mockTrending.map((item, index) => (
                  <div key={index} className="pb-4 border-b last:border-b-0 last:pb-0">
                    <h4 className="text-sm font-medium text-gray-900 mb-2 hover:text-emerald-600 cursor-pointer">
                      {item.title}
                    </h4>
                    <div className="flex items-center justify-between text-xs text-gray-500">
                      <Badge variant="outline" className="text-xs">
                        {item.category}
                      </Badge>
                      <span>{item.views} views</span>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Placeholder for future content */}
            <Card className="bg-gradient-to-br from-emerald-50 to-emerald-100 border-emerald-200">
              <CardContent className="pt-6 text-center">
                <div className="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <FileText className="w-6 h-6 text-white" />
                </div>
                <h4 className="font-semibold text-gray-900 mb-2">More Features Coming</h4>
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
