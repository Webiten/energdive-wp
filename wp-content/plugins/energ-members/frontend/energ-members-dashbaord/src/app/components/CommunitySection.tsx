import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Avatar, AvatarFallback } from "./ui/avatar";
import { 
  TrendingUp, 
  Users, 
  Award,
  Clock,
  Play
} from "lucide-react";
import { useVideos } from "../hooks/useVideos";

/* ---------- Sidebar Data (unchanged) ---------- */
const topContributors = [
  {
    name: "Dr. Emily Watson",
    role: "Energy Policy Analyst",
    contributions: 245,
    reputation: 4890,
    specialty: "Policy & Regulation"
  },
  {
    name: "Michael Zhang",
    role: "Senior Consultant",
    contributions: 198,
    reputation: 4320,
    specialty: "Market Analysis"
  },
  {
    name: "Dr. Sarah Mitchell",
    role: "Technology Researcher",
    contributions: 167,
    reputation: 3950,
    specialty: "Innovation"
  },
];

const trendingTopics = [
  { topic: "Green Hydrogen", discussions: 45, growth: "+23%" },
  { topic: "Grid Modernization", discussions: 38, growth: "+18%" },
  { topic: "Carbon Markets", discussions: 34, growth: "+31%" },
  { topic: "Energy Storage", discussions: 29, growth: "+15%" },
];

export function CommunitySection() {

  // ======= REAL VIDEOS HOOK =======
  const { data: videos = [], loading } = useVideos(12);

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        <div className="mb-8">
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">
            Learning & Videos
          </h1>
          <p className="text-gray-600">
            Curated video insights from energy experts
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">

          {/* ========== LEFT: VIDEO FEED (2 columns) ========== */}
          <div className="lg:col-span-2 space-y-6">

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Play className="w-5 h-5 text-emerald-600" />
                  Latest Videos
                </CardTitle>
              </CardHeader>

              <CardContent>

                {loading && (
                  <p className="text-sm text-gray-500 mb-4">
                    Loading videos...
                  </p>
                )}

                {/* VIDEO GRID */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  {videos.map((v: any) => (
                    <Card key={v.id} className="hover:shadow-md transition-shadow">
                      <CardContent className="pt-4 space-y-3">

                        {v.thumbnail && (
                          <img 
                            src={v.thumbnail}
                            alt={v.title}
                            className="w-full rounded-lg aspect-video object-cover"
                          />
                        )}

                        <h3 className="font-semibold text-gray-900">
                          {v.title}
                        </h3>

                        {v.excerpt && (
                          <p className="text-sm text-gray-600 line-clamp-2">
                            {v.excerpt}
                          </p>
                        )}

                        <div className="flex items-center justify-between text-xs text-gray-500">
                          <Badge variant="outline">Video</Badge>

                          {v.date && (
                            <span className="flex items-center gap-1">
                              <Clock className="w-3 h-3" />
                              {v.date}
                            </span>
                          )}
                        </div>

                        {v.video_url && (
                          <a 
                            href={v.video_url}
                            target="_blank"
                            className="text-emerald-600 text-sm font-medium"
                          >
                            Watch Video →
                          </a>
                        )}

                      </CardContent>
                    </Card>
                  ))}
                </div>

              </CardContent>
            </Card>
          </div>

          {/* ========== RIGHT SIDEBAR (same as before) ========== */}
          <div className="space-y-6">

            {/* Top Contributors */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Award className="w-5 h-5 text-emerald-600" />
                  Top Contributors
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {topContributors.map((c, index) => (
                  <div key={index} className="flex items-start gap-3 pb-4 border-b last:border-b-0">
                    <Avatar className="w-10 h-10">
                      <AvatarFallback className="bg-emerald-600 text-white text-sm">
                        {c.name.split(" ").map(n => n[0]).join("")}
                      </AvatarFallback>
                    </Avatar>

                    <div>
                      <p className="font-medium text-sm">{c.name}</p>
                      <p className="text-xs text-gray-500">{c.role}</p>
                      <Badge variant="outline" className="mt-1 text-xs">
                        {c.specialty}
                      </Badge>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Trending Topics */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <TrendingUp className="w-5 h-5 text-emerald-600" />
                  Trending Topics
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                {trendingTopics.map((t, i) => (
                  <div key={i} className="pb-3 border-b last:border-b-0">
                    <div className="flex items-center justify-between mb-1">
                      <h4 className="font-medium text-sm">{t.topic}</h4>
                      <Badge variant="secondary">{t.growth}</Badge>
                    </div>
                    <p className="text-xs text-gray-500">
                      {t.discussions} discussions
                    </p>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Guidelines (same) */}
            <Card className="bg-gradient-to-br from-emerald-50 to-emerald-100">
              <CardContent className="pt-6">
                <h4 className="font-semibold mb-2 flex items-center gap-2">
                  <Users className="w-5 h-5 text-emerald-600" />
                  Learning Guidelines
                </h4>
                <ul className="text-sm space-y-2">
                  <li>• Watch actively</li>
                  <li>• Take notes</li>
                  <li>• Share insights</li>
                  <li>• Discuss with peers</li>
                </ul>
              </CardContent>
            </Card>
          </div>

        </div>
      </div>
    </div>
  );
}
