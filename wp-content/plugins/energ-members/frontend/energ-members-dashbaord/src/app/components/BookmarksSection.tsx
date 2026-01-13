import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Bookmark, Trash2, ExternalLink } from "lucide-react";
import { Button } from "./ui/button";

const bookmarkedArticles = [
  {
    id: 1,
    title: "Offshore Wind Development: Regulatory Framework Analysis",
    category: "Policy & Regulation",
    author: "Dr. Anna Williams",
    savedDate: "Jan 7, 2026",
    readTime: "14 min",
    excerpt: "Comprehensive overview of permitting processes and regulatory requirements for offshore wind projects."
  },
  {
    id: 2,
    title: "Electric Vehicle Charging Infrastructure: Investment Trends",
    category: "Market & Industry",
    author: "Michael Torres",
    savedDate: "Jan 5, 2026",
    readTime: "11 min",
    excerpt: "Analysis of capital flows into EV charging networks and emerging business models in the sector."
  },
  {
    id: 3,
    title: "Grid Modernization: Lessons from Leading Markets",
    category: "Technology & Innovation",
    author: "Lisa Chen",
    savedDate: "Jan 3, 2026",
    readTime: "16 min",
    excerpt: "Case studies examining successful grid upgrade projects and best practices from around the world."
  }
];

const bookmarkedDiscussions = [
  {
    id: 1,
    title: "What's the realistic timeline for green hydrogen adoption?",
    participants: 45,
    replies: 128,
    savedDate: "Jan 6, 2026",
    lastActivity: "2 hours ago"
  },
  {
    id: 2,
    title: "Nuclear vs Renewables: Can we have both in the energy mix?",
    participants: 67,
    replies: 203,
    savedDate: "Jan 4, 2026",
    lastActivity: "1 day ago"
  }
];

export function BookmarksSection() {
  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8 space-y-8">
        <div>
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">Bookmarks</h1>
          <p className="text-gray-600">Your saved articles and discussions</p>
        </div>

        {/* Saved Articles */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Bookmark className="w-5 h-5 text-emerald-600" />
              Saved Articles ({bookmarkedArticles.length})
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            {bookmarkedArticles.map((article) => (
              <div key={article.id} className="pb-6 border-b last:border-b-0 last:pb-0">
                <div className="flex items-start justify-between mb-3">
                  <div className="flex items-center gap-3">
                    <Badge variant="secondary">{article.category}</Badge>
                    <span className="text-xs text-gray-500">{article.readTime} read</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Button variant="ghost" size="sm">
                      <ExternalLink className="w-4 h-4" />
                    </Button>
                    <Button variant="ghost" size="sm" className="text-red-600 hover:text-red-700 hover:bg-red-50">
                      <Trash2 className="w-4 h-4" />
                    </Button>
                  </div>
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2 hover:text-emerald-600 cursor-pointer">
                  {article.title}
                </h3>
                <p className="text-sm text-gray-600 mb-3">{article.excerpt}</p>
                <div className="flex items-center justify-between text-sm text-gray-500">
                  <span>{article.author}</span>
                  <span>Saved on {article.savedDate}</span>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        {/* Bookmarked Discussions */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Bookmark className="w-5 h-5 text-emerald-600" />
              Bookmarked Discussions ({bookmarkedDiscussions.length})
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            {bookmarkedDiscussions.map((discussion) => (
              <div key={discussion.id} className="pb-4 border-b last:border-b-0 last:pb-0">
                <div className="flex items-start justify-between mb-2">
                  <h3 className="text-base font-semibold text-gray-900 hover:text-emerald-600 cursor-pointer flex-1">
                    {discussion.title}
                  </h3>
                  <Button variant="ghost" size="sm" className="text-red-600 hover:text-red-700 hover:bg-red-50 flex-shrink-0 ml-2">
                    <Trash2 className="w-4 h-4" />
                  </Button>
                </div>
                <div className="flex items-center gap-4 text-sm text-gray-500">
                  <span>{discussion.participants} participants</span>
                  <span>•</span>
                  <span>{discussion.replies} replies</span>
                  <span>•</span>
                  <span>Last activity: {discussion.lastActivity}</span>
                </div>
                <div className="mt-2 text-xs text-gray-500">
                  Saved on {discussion.savedDate}
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        {/* Empty State Message */}
        {bookmarkedArticles.length === 0 && bookmarkedDiscussions.length === 0 && (
          <Card className="text-center py-12">
            <CardContent>
              <Bookmark className="w-12 h-12 text-gray-400 mx-auto mb-4" />
              <h3 className="text-lg font-semibold text-gray-900 mb-2">No bookmarks yet</h3>
              <p className="text-gray-600">
                Start saving articles and discussions to access them quickly later
              </p>
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  );
}