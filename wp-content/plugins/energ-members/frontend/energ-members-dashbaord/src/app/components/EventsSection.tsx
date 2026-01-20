import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Calendar, Clock, MapPin, Users, Video } from "lucide-react";
import { Button } from "./ui/button";

const upcomingEvents = [
  {
    id: 1,
    title: "Energy Storage Summit 2026",
    type: "In-Person Conference",
    date: "Feb 15-17, 2026",
    time: "9:00 AM - 5:00 PM EST",
    location: "Boston Convention Center, MA",
    attendees: "500+",
    description: "Three-day conference exploring the latest advances in battery storage, grid integration, and energy management systems.",
    tags: ["Technology", "Networking", "Exhibition"]
  },
  {
    id: 2,
    title: "Webinar: Carbon Markets 101",
    type: "Webinar",
    date: "Jan 15, 2026",
    time: "2:00 PM - 3:30 PM EST",
    location: "Online",
    attendees: "200+",
    description: "Introduction to carbon credit markets, trading mechanisms, and compliance frameworks for energy professionals.",
    tags: ["Policy", "Markets", "Beginner-Friendly"]
  },
  {
    id: 3,
    title: "Digital Dialogue: The Future of Nuclear Energy",
    type: "Digital Dialogue",
    date: "Jan 20, 2026",
    time: "11:00 AM - 12:00 PM EST",
    location: "Online",
    attendees: "150+",
    description: "Expert panel discussion on next-generation nuclear technologies, SMRs, and the role of nuclear in clean energy transitions.",
    tags: ["Technology", "Policy", "Expert Panel"]
  },
  {
    id: 4,
    title: "Renewable Energy Finance Workshop",
    type: "Workshop",
    date: "Jan 25, 2026",
    time: "10:00 AM - 4:00 PM EST",
    location: "Online",
    attendees: "100+",
    description: "Hands-on workshop covering project finance, investment strategies, and risk assessment for renewable energy projects.",
    tags: ["Finance", "Practical", "Interactive"]
  },
  {
    id: 5,
    title: "Global Energy Outlook 2026",
    type: "Webinar",
    date: "Feb 1, 2026",
    time: "1:00 PM - 2:30 PM EST",
    location: "Online",
    attendees: "300+",
    description: "Annual flagship webinar presenting EnergClub's comprehensive analysis of global energy trends and forecasts.",
    tags: ["Markets", "Analysis", "Flagship Event"]
  }
];

export function EventsSection() {
  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-7xl mx-auto p-6 md:p-8">
        <div className="mb-8">
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">Events</h1>
          <p className="text-gray-600">Webinars, digital dialogues, and industry conferences</p>
        </div>

        <div className="grid gap-6">
          {upcomingEvents.map((event) => (
            <Card key={event.id} className="hover:shadow-lg transition-shadow">
              <CardHeader>
                <div className="flex items-start justify-between mb-3">
                  <Badge variant={event.type === "In-Person Conference" ? "default" : "secondary"}>
                    {event.type}
                  </Badge>
                  <div className="flex flex-wrap gap-2">
                    {event.tags.map((tag, idx) => (
                      <Badge key={idx} variant="outline" className="text-xs">
                        {tag}
                      </Badge>
                    ))}
                  </div>
                </div>
                <CardTitle className="text-2xl">{event.title}</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 mb-6">{event.description}</p>
                
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                  <div className="flex items-center gap-2 text-sm text-gray-700">
                    <Calendar className="w-4 h-4 text-emerald-600" />
                    <span>{event.date}</span>
                  </div>
                  <div className="flex items-center gap-2 text-sm text-gray-700">
                    <Clock className="w-4 h-4 text-emerald-600" />
                    <span>{event.time}</span>
                  </div>
                  <div className="flex items-center gap-2 text-sm text-gray-700">
                    {event.location === "Online" ? (
                      <Video className="w-4 h-4 text-emerald-600" />
                    ) : (
                      <MapPin className="w-4 h-4 text-emerald-600" />
                    )}
                    <span>{event.location}</span>
                  </div>
                  <div className="flex items-center gap-2 text-sm text-gray-700">
                    <Users className="w-4 h-4 text-emerald-600" />
                    <span>{event.attendees} registered</span>
                  </div>
                </div>

                <div className="flex gap-3">
                  <Button className="bg-emerald-600 hover:bg-emerald-700">
                    Register Now
                  </Button>
                  <Button variant="outline">
                    Learn More
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </div>
  );
}