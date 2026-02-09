import { 
  LayoutDashboard, 
  Brain, 
  CreditCard, 
  Calendar, 
  Bookmark, 
  Settings,
  MessageSquare,
  Home
} from "lucide-react";
import { Button } from "./ui/button";

interface SecondHeaderProps {
  activeSection: string;
  onSectionChange: (section: string) => void;
}

const navigationItems = [
  { id: "dashboard", label: "Dashboard", icon: LayoutDashboard },
  { id: "intelligence", label: "Intelligence", icon: Brain },
  { id: "community", label: "Video", icon: MessageSquare },
  { id: "subscriptions", label: "Subscriptions", icon: CreditCard },
  { id: "events", label: "Events", icon: Calendar },
  // { id: "bookmarks", label: "Bookmarks", icon: Bookmark },
  { id: "settings", label: "Account Settings", icon: Settings },
];

export function SecondHeader({ activeSection, onSectionChange }: SecondHeaderProps) {
  return (
    <div className="bg-white border-b sticky top-16 z-40">
      <div className="max-w-7xl mx-auto px-6">
        <div className="flex items-center justify-between h-14">
          {/* Main Site Link */}
          <Button 
            variant="ghost" 
            className="gap-2 text-gray-700 hover:text-emerald-600 hover:bg-emerald-50"
            onClick={() => window.location.href = '/'}
          >
            <Home className="w-4 h-4" />
            <span className="hidden sm:inline">Main Site</span>
          </Button>

          {/* Navigation Items */}
          <nav className="flex items-center gap-1 overflow-x-auto">
            {navigationItems.map((item) => {
              const Icon = item.icon;
              const isActive = activeSection === item.id;
              
              return (
                <button
                  key={item.id}
                  onClick={() => onSectionChange(item.id)}
                  className={`
                    flex items-center gap-2 px-4 py-2 rounded-lg transition-all whitespace-nowrap
                    ${isActive 
                      ? "bg-emerald-50 text-emerald-700 font-medium" 
                      : "text-gray-700 hover:bg-gray-50 hover:text-gray-900"
                    }
                  `}
                >
                  <Icon className={`w-4 h-4 ${isActive ? "text-emerald-600" : "text-gray-500"}`} />
                  <span className="text-sm hidden md:inline">{item.label}</span>
                  <span className="text-sm md:hidden">{item.label.split(' ')[0]}</span>
                </button>
              );
            })}
          </nav>
        </div>
      </div>
    </div>
  );
}
