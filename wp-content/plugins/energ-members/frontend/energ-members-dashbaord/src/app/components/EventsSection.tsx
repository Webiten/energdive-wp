import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Badge } from "./ui/badge";
import { Calendar, Clock, MapPin, Users, Video } from "lucide-react";
import { Button } from "./ui/button";
import { ImageWithFallback } from "@/app/components/figma/ImageWithFallback";

const upcomingEvents = [
  {
    id: 1,
    title: "India Energy Week 2026",
    type: "In-Person Conference",
    date: "Jan 27-30, 2026",
    time: "Full Day Event",
    location: "Goa, India",
    attendees: "5000+",
    description: "Now in its 4th edition, India Energy Week will take place from 27 – 30 January 2026 in Goa, under the patronage of India's Ministry of Petroleum and Natural Gas. As India strengthens its role at the heart of the global energy transformation, India Energy Week 2026 will unite policymakers, business leaders, innovators and investors to drive pragmatic solutions for a secure, sustainable and affordable energy future.",
    tags: ["Policy", "Networking", "Exhibition"],
    logo: "https://energ.energdive.com/wp-content/uploads/2025/12/iew-logo.png",
    registerUrl: "https://stage.energdive.com/events/india-energy-week-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/india-energy-week-2026"
  },
  {
    id: 2,
    title: "International Process Safety Conference 2026",
    type: "Conference",
    date: "Feb 26, 2026",
    time: "Full Day Event",
    location: "Hyatt Regency, New Delhi",
    attendees: "500+",
    description: "The seventh edition of the International Process Safety Conference (INPSC) convenes at a pivotal moment—when India's energy and process industries are not just responding to transformation, but are poised to lead it. As our sectors embrace decarbonization, digitization, and decentralization, one imperative stands immutable: Process safety is national capacity.",
    tags: ["Safety", "Industry", "Best Practices"],
    logo: "https://energ.energdive.com/wp-content/uploads/2025/12/inpsc-logo-1.png",
    registerUrl: "https://stage.energdive.com/events/international-process-safety-conference-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/international-process-safety-conference-2026"
  },
  {
    id: 3,
    title: "Bharat Fire Safety Congress 2026",
    type: "In-Person Conference",
    date: "May 14-15, 2026",
    time: "Full Day Event",
    location: "Yashobhoomi IICC, Dwarka, New Delhi",
    attendees: "800+",
    description: "Fire safety is no longer a reactive function—it is a cornerstone of national resilience, urban transformation, and sustainable development. As India moves decisively toward becoming a developed nation by 2047, the modernization and institutional strengthening of Fire Services must keep pace with the dynamic growth of our infrastructure, industries, and population.",
    tags: ["Fire Safety", "Infrastructure", "Compliance"],
    logo: "https://www.energdive.com/wp-content/uploads/2025/12/bsf-logo-1.png",
    registerUrl: "https://stage.energdive.com/events/bharat-fire-safety-congress-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/bharat-fire-safety-congress-2026"
  },
  {
    id: 4,
    title: "Global Refining & Petrochemicals Congress (GRPC) 2026",
    type: "In-Person Conference",
    date: "Jun 18-19, 2026",
    time: "Full Day Event",
    location: "Le Méridien, New Delhi",
    attendees: "600+",
    description: "The world is entering a decisive decade for energy — one that will determine how nations grow, industries compete, and societies sustain themselves in an era defined by climate responsibility and technological disruption. Amidst this transformation, India's refining and petrochemicals sector stands as both anchor and architect of the country's energy future.",
    tags: ["Refining", "Petrochemicals", "Decarbonization"],
    logo: "https://energ.energdive.com/wp-content/uploads/2025/12/grpc-logo-1.png",
    registerUrl: "https://stage.energdive.com/events/global-refining-petrochemicals-congress-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/global-refining-petrochemicals-congress-2026"
  },
  {
    id: 5,
    title: "Transform HSE 2026",
    type: "Conference",
    date: "Aug 6-7, 2026",
    time: "Full Day Event",
    location: "Hyatt Regency, New Delhi",
    attendees: "400+",
    description: "Welcome to Transform HSE, where we set our sights on 'The Next Frontier: Advancing HSE to Achieve Global SDGs' a bold and forward-thinking theme that reflects the urgency and potential of Health, Safety, and Environment (HSE) in the global sustainability journey. Beyond safeguarding operations, HSE is emerging as a central enabler for achieving the United Nations's Sustainable Development Goals (SDGs).",
    tags: ["HSE", "Sustainability", "SDGs"],
    logo: "https://energ.energdive.com/wp-content/uploads/2025/12/hse-logo-1.png",
    registerUrl: "https://stage.energdive.com/events/transform-hse-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/transform-hse-2026"
  },
  {
    id: 6,
    title: "Bharat Electricity 2026",
    type: "Exhibition & Conference",
    date: "Sep 1-3, 2026",
    time: "Full Day Event",
    location: "Yashobhoomi IICC, Dwarka, New Delhi",
    attendees: "3000+",
    description: "In September 2026, the energy sector supply chain will come together at Bharat Electricity, POWERGEN India & Indian Utility Week. Co-located at the Yashobhoomi, IICC, Dwarka in New Delhi, they will form one fully integrated event, underpinned by a high-level Strategic Summit and Exhibition over 3 days.",
    tags: ["Power Generation", "Utilities", "Exhibition"],
    logo: "https://energ.energdive.com/wp-content/uploads/2025/12/bharat-electricity-logo.png",
    registerUrl: "https://stage.energdive.com/events/bharat-electricity-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/bharat-electricity-2026"
  },
  {
    id: 7,
    title: "Oil Spill India 2026",
    type: "Conference",
    date: "Oct 6-7, 2026",
    time: "Full Day Event",
    location: "Hotel JW Marriott, Aerocity, Delhi",
    attendees: "350+",
    description: "Welcome to the 8th edition of Oil Spill India (OSI), set against the vibrant backdrop of New Delhi during 6th - 7th October 2026. Since its inception in 2011, OSI—with foundational leadership from the Indian Coast Guard and ONGC Limited—has evolved into one of the world's leading forums dedicated to Spill Prevention, Planning, Preparedness, Response, and Restoration.",
    tags: ["Environmental", "Response", "Marine Safety"],
    logo: "https://energ.energdive.com/wp-content/uploads/2025/12/osi-logo.png",
    registerUrl: "https://stage.energdive.com/events/oil-spill-india-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/oil-spill-india-2026"
  },
  {
    id: 8,
    title: "EnergNiti Dialogue 2026",
    type: "Strategic Dialogue",
    date: "Dec 18, 2026",
    time: "Full Day Event",
    location: "New Delhi",
    attendees: "250+",
    description: "India's energy journey is entering a new epoch — one defined not just by scale and self-reliance, but by ambition, innovation, and global stewardship. Through high-level keynotes, strategic dialogues, and focused sessions, this platform will unlock policy imperatives, investment directions, and collaborative pathways critical for India@2047 — where energy is not just a commodity, but a cornerstone of nation-building and diplomacy.",
    tags: ["Policy", "Strategy", "India@2047"],
    logo: "https://energ.energdive.com/wp-content/uploads/2025/12/energniti-logo-1.png",
    registerUrl: "https://stage.energdive.com/events/energniti-dialogue-2026/register",
    learnMoreUrl: "https://stage.energdive.com/events/energniti-dialogue-2026"
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
                <div className="flex items-start gap-4 mb-4">
                  <ImageWithFallback 
                    src={event.logo}
                    alt={`${event.title} logo`}
                    className="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                  />
                  <div className="flex-1">
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
                  </div>
                </div>
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
                  <Button className="bg-emerald-600 hover:bg-emerald-700" asChild>
                    <a href={event.registerUrl} target="_blank" rel="noopener noreferrer">
                      Register Now
                    </a>
                  </Button>
                  <Button variant="outline" asChild>
                    <a href={event.learnMoreUrl} target="_blank" rel="noopener noreferrer">
                      Learn More
                    </a>
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