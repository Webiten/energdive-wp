import { useState } from "react";

// Auth Components
import { LoginPage } from "./components/auth/LoginPage";
import { VerificationPage } from "./components/auth/VerificationPage";
import { RegisterPage } from "./components/auth/RegisterPage";
import { RegistrationSuccess } from "./components/auth/RegistrationSuccess";

// User Dashboard Components
import { TopBar } from "./components/TopBar";
import { SecondHeader } from "./components/SecondHeader";
import { DashboardHome } from "./components/DashboardHome";
import { IntelligenceSection } from "./components/IntelligenceSection";
import { CommunitySection } from "./components/CommunitySection";
import { SubscriptionsSection } from "./components/SubscriptionsSection";
import { EventsSection } from "./components/EventsSection";
import { BookmarksSection } from "./components/BookmarksSection";
import { AccountSettingsSection } from "./components/AccountSettingsSection";

type AppState = 
  | 'login' 
  | 'verification' 
  | 'register' 
  | 'registration-success' 
  | 'dashboard';

export default function App() {
  const [appState, setAppState] = useState<AppState>('login');
  const [activeSection, setActiveSection] = useState("dashboard");
  const [userEmail, setUserEmail] = useState("");
  const [requiresApproval, setRequiresApproval] = useState(false);

  // Auth Flow Handlers
  const handleVerificationSent = (email: string) => {
    setUserEmail(email);
    setAppState('verification');
  };

  const handleEmailVerified = (isNewUser: boolean) => {
    if (isNewUser) {
      setAppState('register');
    } else {
      setAppState('dashboard');
    }
  };

  const handleRegistrationComplete = () => {
    // Simulate: some users require approval, some don't
    const needsApproval = Math.random() > 0.5;
    setRequiresApproval(needsApproval);
    setAppState('registration-success');
  };

  const handleContinueToDashboard = () => {
    if (!requiresApproval) {
      setAppState('dashboard');
    } else {
      // Return to login for now (user will be notified via email)
      setAppState('login');
    }
  };

  const handleResendVerification = () => {
    console.log('Resending verification email to:', userEmail);
  };

  // Dashboard Content Renderer
  const renderDashboardContent = () => {
    switch (activeSection) {
      case "dashboard":
        return <DashboardHome />;
      case "intelligence":
        return <IntelligenceSection />;
      case "community":
        return <CommunitySection />;
      case "subscriptions":
        return <SubscriptionsSection />;
      case "events":
        return <EventsSection />;
      case "bookmarks":
        return <BookmarksSection />;
      case "settings":
        return <AccountSettingsSection />;
      default:
        return <DashboardHome />;
    }
  };

  // Render Dashboard
  if (appState === 'dashboard') {
    return (
      <div className="size-full bg-gray-50">
        <TopBar onLogout={() => setAppState('login')} />
        <SecondHeader 
          activeSection={activeSection} 
          onSectionChange={setActiveSection} 
        />
        {renderDashboardContent()}
      </div>
    );
  }

  // Render Auth Flow
  return (
    <div className="size-full">
      {appState === 'login' && (
        <LoginPage onVerificationSent={handleVerificationSent} />
      )}
      
      {appState === 'verification' && (
        <VerificationPage
          email={userEmail}
          onVerified={handleEmailVerified}
          onResend={handleResendVerification}
        />
      )}
      
      {appState === 'register' && (
        <RegisterPage
          email={userEmail}
          onRegistrationComplete={handleRegistrationComplete}
        />
      )}
      
      {appState === 'registration-success' && (
        <RegistrationSuccess
          requiresApproval={requiresApproval}
          onContinue={handleContinueToDashboard}
        />
      )}
    </div>
  );
}