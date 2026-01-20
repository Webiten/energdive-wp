import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Button } from "./ui/button";
import { Avatar, AvatarFallback } from "./ui/avatar";
import { Textarea } from "./ui/textarea";
import { 
  ThumbsUp, 
  MessageSquare, 
  Bookmark,
  Share2,
  MoreVertical,
  Reply,
  Award
} from "lucide-react";
import { Separator } from "./ui/separator";

const mockDiscussion = {
  title: "What's the realistic timeline for green hydrogen adoption in heavy industry?",
  author: "Dr. Michael Chen",
  authorRole: "Industry Practitioner",
  category: "Technology & Innovation",
  tags: ["Hydrogen", "Decarbonization", "Heavy Industry"],
  createdAt: "Jan 8, 2026",
  views: 3420,
  likes: 89,
  bookmarks: 34,
  content: `I've been researching hydrogen adoption pathways and would love to hear perspectives on realistic timelines for widespread adoption in cement, steel, and chemical sectors.

From my analysis, the key barriers seem to be:
1. Production cost parity with grey hydrogen
2. Infrastructure development (pipelines, storage)
3. End-use equipment conversion costs
4. Policy support and carbon pricing mechanisms

What are your thoughts on when we might see meaningful penetration (>10% of energy use) in these hard-to-abate sectors? Are we looking at 2030, 2035, or beyond?

I'm particularly interested in hearing from practitioners who are working on actual projects in these industries.`
};

const mockReplies = [
  {
    id: 1,
    author: "Dr. Sarah Williams",
    authorRole: "Policy & Institutional User",
    content: "Great question! From a policy perspective, I think 2035 is more realistic for >10% penetration in Europe. The EU's hydrogen strategy and upcoming CBAM regulations will create strong incentives, but the infrastructure buildout is the long pole. We're seeing promising pilot projects in steel production, but scaling will take time.",
    likes: 45,
    createdAt: "Jan 8, 2026 - 3 hours ago",
    isExpert: true,
    replies: 3
  },
  {
    id: 2,
    author: "James Rodriguez",
    authorRole: "Consultant & Analyst",
    content: "I've been modeling this for clients in the cement industry. My base case is 2033-2035 for 10% penetration, with an optimistic scenario hitting that mark in 2030-2031. The key variable is carbon pricing - if we see $100+/ton CO2, the economics shift dramatically. Current projects I'm tracking show a breakeven point around $75-85/ton with existing technology.",
    likes: 32,
    createdAt: "Jan 8, 2026 - 5 hours ago",
    isExpert: false,
    replies: 2
  },
  {
    id: 3,
    author: "Lisa Anderson",
    authorRole: "Industry Practitioner",
    content: "Working on a steel plant hydrogen retrofit project right now. Honestly, the timeline depends heavily on regional factors. In regions with cheap renewable electricity (think Middle East, parts of Australia), we could see faster adoption - maybe 2030-2032. In Europe and North America, I'd say 2035+ is more realistic given grid constraints and regulatory timelines.",
    likes: 28,
    createdAt: "Jan 8, 2026 - 1 day ago",
    isExpert: true,
    replies: 1
  },
  {
    id: 4,
    author: "Dr. Rajesh Kumar",
    authorRole: "Technology Researcher",
    content: "Don't forget about the technology learning curve. Electrolyzer costs are still declining rapidly. If we see costs drop another 40-50% by 2028-2029 (which current trajectories suggest), the economics become much more favorable. I'd bet on 2032-2035 for meaningful adoption in chemicals and refining, possibly earlier in regions with strong policy support.",
    likes: 41,
    createdAt: "Jan 8, 2026 - 1 day ago",
    isExpert: true,
    replies: 4
  }
];

export function DiscussionDetail() {
  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-5xl mx-auto p-6 md:p-8 space-y-6">
        {/* Discussion Header */}
        <Card>
          <CardHeader>
            <div className="flex items-start gap-4">
              <Avatar className="w-14 h-14 flex-shrink-0">
                <AvatarFallback className="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white">
                  MC
                </AvatarFallback>
              </Avatar>
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-2">
                  <Badge variant="secondary">{mockDiscussion.category}</Badge>
                  {mockDiscussion.tags.map((tag, idx) => (
                    <Badge key={idx} variant="outline" className="text-xs">
                      {tag}
                    </Badge>
                  ))}
                </div>
                <CardTitle className="text-2xl mb-2">{mockDiscussion.title}</CardTitle>
                <div className="flex items-center gap-3 text-sm text-gray-600">
                  <span className="font-medium">{mockDiscussion.author}</span>
                  <span>•</span>
                  <span>{mockDiscussion.authorRole}</span>
                  <span>•</span>
                  <span>{mockDiscussion.createdAt}</span>
                </div>
              </div>
              <Button variant="ghost" size="sm">
                <MoreVertical className="w-5 h-5" />
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <div className="prose prose-sm max-w-none mb-6">
              {mockDiscussion.content.split('\n').map((paragraph, idx) => (
                <p key={idx} className="text-gray-700 mb-3 whitespace-pre-wrap">
                  {paragraph}
                </p>
              ))}
            </div>

            <Separator className="my-6" />

            {/* Engagement Actions */}
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-6">
                <Button variant="ghost" size="sm" className="gap-2">
                  <ThumbsUp className="w-4 h-4" />
                  <span>{mockDiscussion.likes} Likes</span>
                </Button>
                <Button variant="ghost" size="sm" className="gap-2">
                  <MessageSquare className="w-4 h-4" />
                  <span>{mockReplies.length} Replies</span>
                </Button>
                <Button variant="ghost" size="sm" className="gap-2">
                  <Bookmark className="w-4 h-4" />
                  <span>{mockDiscussion.bookmarks}</span>
                </Button>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-sm text-gray-500">{mockDiscussion.views} views</span>
                <Button variant="ghost" size="sm">
                  <Share2 className="w-4 h-4" />
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Replies */}
        <Card>
          <CardHeader>
            <CardTitle>{mockReplies.length} Replies</CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            {mockReplies.map((reply) => (
              <div key={reply.id} className="pb-6 border-b last:border-b-0 last:pb-0">
                <div className="flex gap-4">
                  <Avatar className="w-10 h-10 flex-shrink-0">
                    <AvatarFallback className="bg-gradient-to-br from-blue-400 to-blue-600 text-white">
                      {reply.author.split(' ').map(n => n[0]).join('')}
                    </AvatarFallback>
                  </Avatar>
                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-2">
                      <span className="font-medium text-gray-900">{reply.author}</span>
                      {reply.isExpert && (
                        <Badge variant="outline" className="text-xs bg-blue-50 text-blue-700 border-blue-200">
                          <Award className="w-3 h-3 mr-1" />
                          Expert
                        </Badge>
                      )}
                      <span className="text-sm text-gray-500">• {reply.authorRole}</span>
                    </div>
                    <p className="text-sm text-gray-700 mb-3 whitespace-pre-wrap">{reply.content}</p>
                    <div className="flex items-center gap-4">
                      <Button variant="ghost" size="sm" className="h-8 gap-1 text-xs">
                        <ThumbsUp className="w-3 h-3" />
                        <span>{reply.likes}</span>
                      </Button>
                      <Button variant="ghost" size="sm" className="h-8 gap-1 text-xs">
                        <Reply className="w-3 h-3" />
                        <span>Reply</span>
                      </Button>
                      {reply.replies > 0 && (
                        <span className="text-xs text-gray-500">{reply.replies} replies</span>
                      )}
                      <span className="text-xs text-gray-500 ml-auto">{reply.createdAt}</span>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>

        {/* Reply Input */}
        <Card>
          <CardContent className="pt-6">
            <div className="flex gap-4">
              <Avatar className="w-10 h-10 flex-shrink-0">
                <AvatarFallback className="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white">
                  JD
                </AvatarFallback>
              </Avatar>
              <div className="flex-1 space-y-3">
                <Textarea
                  placeholder="Share your insights and expertise..."
                  className="min-h-[120px]"
                />
                <div className="flex items-center justify-between">
                  <p className="text-xs text-gray-500">
                    Be respectful and constructive in your response
                  </p>
                  <Button className="bg-emerald-600 hover:bg-emerald-700">
                    Post Reply
                  </Button>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}