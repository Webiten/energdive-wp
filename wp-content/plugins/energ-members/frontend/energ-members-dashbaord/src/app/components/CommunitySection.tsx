import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Button } from "./ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./ui/tabs";
import { Avatar, AvatarFallback } from "./ui/avatar";
import { 
  MessageSquare, 
  TrendingUp, 
  Users, 
  Award,
  ThumbsUp,
  Reply,
  Eye,
  Clock
} from "lucide-react";

const discussions = [
  {
    id: 1,
    title: "What's the realistic timeline for green hydrogen adoption in heavy industry?",
    author: "Dr. Michael Chen",
    authorRole: "Industry Practitioner",
    category: "Technology & Innovation",
    tags: ["Hydrogen", "Decarbonization", "Heavy Industry"],
    replies: 128,
    views: 3420,
    likes: 89,
    lastActivity: "2 hours ago",
    excerpt: "I've been researching hydrogen adoption pathways and would love to hear perspectives on realistic timelines for widespread adoption in cement, steel, and chemical sectors...",
    isPinned: false,
    isHot: true
  },
  {
    id: 2,
    title: "Nuclear vs Renewables: Can we have both in the energy mix?",
    author: "Sarah Williams",
    authorRole: "Policy & Institutional User",
    category: "Policy & Regulation",
    tags: ["Nuclear", "Renewables", "Energy Mix"],
    replies: 203,
    views: 5680,
    likes: 156,
    lastActivity: "1 day ago",
    excerpt: "The debate continues on optimal energy portfolios. Should we pursue both nuclear and renewables, or focus resources on one path? Let's discuss the policy and economic implications...",
    isPinned: true,
    isHot: true
  },
  {
    id: 3,
    title: "Best practices for community solar project development",
    author: "James Rodriguez",
    authorRole: "Consultant & Analyst",
    category: "Market & Industry",
    tags: ["Solar", "Community Energy", "Project Development"],
    replies: 67,
    views: 1890,
    likes: 45,
    lastActivity: "3 hours ago",
    excerpt: "Looking to gather insights on successful community solar models. What are the key factors that make these projects successful from a financing and community engagement perspective?",
    isPinned: false,
    isHot: false
  },
  {
    id: 4,
    title: "Carbon offset verification: Current challenges and solutions",
    author: "Dr. Priya Sharma",
    authorRole: "CXO & Senior Leader",
    category: "Policy & Regulation",
    tags: ["Carbon Markets", "Verification", "Compliance"],
    replies: 91,
    views: 2340,
    likes: 72,
    lastActivity: "5 hours ago",
    excerpt: "With growing scrutiny on carbon offset quality, what verification standards and practices are proving most reliable? Interested in both voluntary and compliance markets...",
    isPinned: false,
    isHot: true
  },
  {
    id: 5,
    title: "Grid flexibility: How are different markets solving intermittency?",
    author: "Alex Kumar",
    authorRole: "Early Professional",
    category: "Technology & Innovation",
    tags: ["Grid Management", "Energy Storage", "Flexibility"],
    replies: 145,
    views: 4120,
    likes: 98,
    lastActivity: "6 hours ago",
    excerpt: "Comparing approaches to grid flexibility across regions. What solutions are working best - batteries, demand response, interconnection, or hybrid approaches?",
    isPinned: false,
    isHot: false
  },
  {
    id: 6,
    title: "EV charging infrastructure: Business models that work",
    author: "Maria Garcia",
    authorRole: "Consultant & Analyst",
    category: "Market & Industry",
    tags: ["Electric Vehicles", "Infrastructure", "Business Models"],
    replies: 112,
    views: 3580,
    likes: 83,
    lastActivity: "12 hours ago",
    excerpt: "What charging infrastructure business models are proving viable? Looking at fleet charging, public charging, and workplace solutions...",
    isPinned: false,
    isHot: false
  }
];

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
  {
    name: "Carlos Rodriguez",
    role: "Industry Practitioner",
    contributions: 143,
    reputation: 3410,
    specialty: "Operations"
  },
  {
    name: "Dr. Aisha Patel",
    role: "Climate Economist",
    contributions: 128,
    reputation: 3180,
    specialty: "Economics"
  }
];

const trendingTopics = [
  { topic: "Green Hydrogen", discussions: 45, growth: "+23%" },
  { topic: "Grid Modernization", discussions: 38, growth: "+18%" },
  { topic: "Carbon Markets", discussions: 34, growth: "+31%" },
  { topic: "Energy Storage", discussions: 29, growth: "+15%" },
  { topic: "Nuclear SMRs", discussions: 24, growth: "+42%" },
];

export function CommunitySection() {
  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        <div className="mb-8">
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">Community & Discussions</h1>
          <p className="text-gray-600">Connect with energy professionals and share insights</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Discussion Area - 2 columns */}
          <div className="lg:col-span-2 space-y-6">
            <div className="flex items-center justify-between">
              <Tabs defaultValue="all" className="w-full">
                <div className="flex items-center justify-between mb-6">
                  <TabsList>
                    <TabsTrigger value="all">All Discussions</TabsTrigger>
                    <TabsTrigger value="hot">Hot Topics</TabsTrigger>
                    <TabsTrigger value="recent">Recent</TabsTrigger>
                    <TabsTrigger value="following">Following</TabsTrigger>
                  </TabsList>
                  <Button className="bg-emerald-600 hover:bg-emerald-700">
                    <MessageSquare className="w-4 h-4 mr-2" />
                    New Discussion
                  </Button>
                </div>

                <TabsContent value="all" className="space-y-4">
                  {discussions.map((discussion) => (
                    <Card key={discussion.id} className="hover:shadow-md transition-shadow cursor-pointer">
                      <CardContent className="pt-6">
                        <div className="flex gap-4">
                          {/* Avatar */}
                          <Avatar className="w-12 h-12 flex-shrink-0">
                            <AvatarFallback className="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white">
                              {discussion.author.split(' ').map(n => n[0]).join('')}
                            </AvatarFallback>
                          </Avatar>

                          {/* Content */}
                          <div className="flex-1 min-w-0">
                            <div className="flex items-start justify-between gap-4 mb-2">
                              <div className="flex-1">
                                <div className="flex items-center gap-2 mb-2">
                                  {discussion.isPinned && (
                                    <Badge className="bg-blue-600">Pinned</Badge>
                                  )}
                                  {discussion.isHot && (
                                    <Badge className="bg-orange-600">
                                      <TrendingUp className="w-3 h-3 mr-1" />
                                      Hot
                                    </Badge>
                                  )}
                                  <Badge variant="secondary">{discussion.category}</Badge>
                                </div>
                                <h3 className="text-lg font-semibold text-gray-900 hover:text-emerald-600 mb-2">
                                  {discussion.title}
                                </h3>
                              </div>
                            </div>

                            <p className="text-sm text-gray-600 mb-3 line-clamp-2">
                              {discussion.excerpt}
                            </p>

                            {/* Tags */}
                            <div className="flex flex-wrap gap-2 mb-3">
                              {discussion.tags.map((tag, idx) => (
                                <Badge key={idx} variant="outline" className="text-xs">
                                  {tag}
                                </Badge>
                              ))}
                            </div>

                            {/* Meta Info */}
                            <div className="flex items-center justify-between">
                              <div className="flex items-center gap-4 text-sm text-gray-600">
                                <span className="font-medium">{discussion.author}</span>
                                <span className="text-xs text-gray-500">•</span>
                                <span className="text-xs">{discussion.authorRole}</span>
                              </div>
                            </div>

                            <div className="flex items-center gap-4 mt-3 text-sm text-gray-500">
                              <div className="flex items-center gap-1">
                                <MessageSquare className="w-4 h-4" />
                                <span>{discussion.replies}</span>
                              </div>
                              <div className="flex items-center gap-1">
                                <Eye className="w-4 h-4" />
                                <span>{discussion.views}</span>
                              </div>
                              <div className="flex items-center gap-1">
                                <ThumbsUp className="w-4 h-4" />
                                <span>{discussion.likes}</span>
                              </div>
                              <div className="flex items-center gap-1 ml-auto">
                                <Clock className="w-4 h-4" />
                                <span>{discussion.lastActivity}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </TabsContent>

                <TabsContent value="hot" className="space-y-4">
                  {discussions.filter(d => d.isHot).map((discussion) => (
                    <Card key={discussion.id} className="hover:shadow-md transition-shadow cursor-pointer">
                      <CardContent className="pt-6">
                        <div className="flex gap-4">
                          <Avatar className="w-12 h-12 flex-shrink-0">
                            <AvatarFallback className="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white">
                              {discussion.author.split(' ').map(n => n[0]).join('')}
                            </AvatarFallback>
                          </Avatar>
                          <div className="flex-1 min-w-0">
                            <Badge className="bg-orange-600 mb-2">
                              <TrendingUp className="w-3 h-3 mr-1" />
                              Hot Topic
                            </Badge>
                            <h3 className="text-lg font-semibold text-gray-900 hover:text-emerald-600 mb-2">
                              {discussion.title}
                            </h3>
                            <p className="text-sm text-gray-600 mb-3">{discussion.excerpt}</p>
                            <div className="flex items-center gap-4 text-sm text-gray-500">
                              <div className="flex items-center gap-1">
                                <MessageSquare className="w-4 h-4" />
                                <span>{discussion.replies}</span>
                              </div>
                              <div className="flex items-center gap-1">
                                <ThumbsUp className="w-4 h-4" />
                                <span>{discussion.likes}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </TabsContent>

                <TabsContent value="recent" className="text-center py-12">
                  <MessageSquare className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-600">Most recent discussions will appear here</p>
                </TabsContent>

                <TabsContent value="following" className="text-center py-12">
                  <Users className="w-12 h-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-600">Discussions you're following will appear here</p>
                </TabsContent>
              </Tabs>
            </div>
          </div>

          {/* Sidebar - 1 column */}
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
                {topContributors.map((contributor, index) => (
                  <div key={index} className="flex items-start gap-3 pb-4 border-b last:border-b-0 last:pb-0">
                    <div className="relative">
                      <Avatar className="w-10 h-10">
                        <AvatarFallback className="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white text-sm">
                          {contributor.name.split(' ').map(n => n[0]).join('')}
                        </AvatarFallback>
                      </Avatar>
                      {index < 3 && (
                        <div className="absolute -top-1 -right-1 w-5 h-5 bg-amber-400 rounded-full flex items-center justify-center text-xs font-bold text-white border-2 border-white">
                          {index + 1}
                        </div>
                      )}
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="font-medium text-sm text-gray-900 truncate">{contributor.name}</p>
                      <p className="text-xs text-gray-500 mb-1">{contributor.role}</p>
                      <div className="flex items-center gap-2 text-xs text-gray-600">
                        <Badge variant="outline" className="text-xs">{contributor.specialty}</Badge>
                      </div>
                      <p className="text-xs text-gray-500 mt-1">
                        {contributor.contributions} contributions • {contributor.reputation} rep
                      </p>
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
                {trendingTopics.map((topic, index) => (
                  <div key={index} className="pb-3 border-b last:border-b-0 last:pb-0">
                    <div className="flex items-center justify-between mb-1">
                      <h4 className="font-medium text-sm text-gray-900">{topic.topic}</h4>
                      <Badge variant="secondary" className="text-xs bg-emerald-100 text-emerald-700">
                        {topic.growth}
                      </Badge>
                    </div>
                    <p className="text-xs text-gray-500">{topic.discussions} active discussions</p>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Community Guidelines */}
            <Card className="bg-gradient-to-br from-emerald-50 to-emerald-100 border-emerald-200">
              <CardContent className="pt-6">
                <h4 className="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                  <Users className="w-5 h-5 text-emerald-600" />
                  Community Guidelines
                </h4>
                <ul className="text-sm text-gray-700 space-y-2">
                  <li>• Be respectful and professional</li>
                  <li>• Share insights and data</li>
                  <li>• Stay on topic</li>
                  <li>• Cite sources when relevant</li>
                </ul>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}