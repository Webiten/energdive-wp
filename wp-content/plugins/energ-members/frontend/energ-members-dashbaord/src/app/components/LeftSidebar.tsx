import { 
  LayoutDashboard, 
  Brain, 
  CreditCard, 
  Calendar, 
  Bookmark, 
  Settings,
  MessageSquare
} from "lucide-react";

interface LeftSidebarProps {
  activeSection: string;
  onSectionChange: (section: string) => void;
}

const navigationItems = [
  { id: "dashboard", label: "Dashboard", icon: LayoutDashboard },
  { id: "intelligence", label: "Intelligence", icon: Brain },
  { id: "community", label: "Community", icon: MessageSquare },
  { id: "subscriptions", label: "Subscriptions", icon: CreditCard },
  { id: "events", label: "Events", icon: Calendar },
  { id: "bookmarks", label: "Bookmarks", icon: Bookmark },
  { id: "settings", label: "Account Settings", icon: Settings },
];

export function LeftSidebar({ activeSection, onSectionChange }: LeftSidebarProps) {
  return (
    <div className="w-64 bg-white border-r h-[calc(100vh-4rem)] sticky top-16">
      <nav className="p-4 space-y-1">
        {navigationItems.map((item) => {
          const Icon = item.icon;
          const isActive = activeSection === item.id;
          
          return (
            <button
              key={item.id}
              onClick={() => onSectionChange(item.id)}
              className={`
                w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-all
                ${isActive 
                  ? "bg-emerald-50 text-emerald-700 font-medium" 
                  : "text-gray-700 hover:bg-gray-50"
                }
              `}
            >
              <Icon className={`w-5 h-5 ${isActive ? "text-emerald-600" : "text-gray-500"}`} />
              <span>{item.label}</span>
            </button>
          );
        })}
      </nav>
    </div>
  );
}