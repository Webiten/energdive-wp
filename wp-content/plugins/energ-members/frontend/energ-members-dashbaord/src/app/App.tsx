import { useState } from "react";

// Auth Components
import { LoginPage } from "./components/auth/LoginPage";
import { VerificationPage } from "./components/auth/VerificationPage";
import { RegisterPage } from "./components/auth/RegisterPage";
import { RegistrationSuccess } from "./components/auth/RegistrationSuccess";

// Dashboard Components
import { TopBar } from "./components/TopBar";
import { SecondHeader } from "./components/SecondHeader";
import { DashboardHome } from "./components/DashboardHome";
import { IntelligenceSection } from "./components/IntelligenceSection";
import { CommunitySection } from "./components/CommunitySection";
import { SubscriptionsSection } from "./components/SubscriptionsSection";
import { EventsSection } from "./components/EventsSection";
import { BookmarksSection } from "./components/BookmarksSection";
import { AccountSettingsSection } from "./components/AccountSettingsSection";

// API
import { AuthAPI } from "./lib/api";

type AppState =
  | "login"
  | "verification"
  | "register"
  | "registration-success"
  | "dashboard";

export default function App() {
  const [appState, setAppState] = useState<AppState>("login");
  const [activeSection, setActiveSection] = useState("dashboard");

  // 🔑 SINGLE SOURCE OF TRUTH
  const [identifier, setIdentifier] = useState<string>("");

  const [requiresApproval, setRequiresApproval] = useState(false);

  /* =======================
     AUTH FLOW HANDLERS
  ======================= */

  const handleVerificationSent = (value: string) => {
    setIdentifier(value);           // ✅ email OR phone
    setAppState("verification");
  };

  const handleVerified = (isNewUser: boolean) => {
    if (isNewUser) {
      setAppState("register");
    } else {
      setAppState("dashboard");
    }
  };

  const handleResendVerification = async () => {
    if (!identifier) return;
    await AuthAPI.requestOtp(identifier);
  };

  const handleRegistrationComplete = () => {
    setRequiresApproval(false);      // safety
    setAppState("dashboard");        // ✅ Direct to dashboard
  };

  const handleContinueToDashboard = () => {
    if (!requiresApproval) {
      setAppState("dashboard");
    } else {
      setAppState("login");
    }
  };

  /* =======================
     DASHBOARD RENDER
  ======================= */

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

  if (appState === "dashboard") {
    return (
      <div className="size-full bg-gray-50">
        <TopBar onLogout={() => setAppState("login")} />
        <SecondHeader
          activeSection={activeSection}
          onSectionChange={setActiveSection}
        />
        {renderDashboardContent()}
      </div>
    );
  }

  /* =======================
     AUTH FLOW RENDER
  ======================= */

  return (
    <div className="size-full">
      {appState === "login" && (
        <LoginPage onVerificationSent={handleVerificationSent} />
      )}

      {appState === "verification" && (
        <VerificationPage
          identifier={identifier}        // ✅ FIXED
          onVerified={handleVerified}
          onResend={handleResendVerification}
        />
      )}

      {appState === "register" && (
        <RegisterPage
          identifier={identifier}        // (if needed later)
          onRegistrationComplete={handleRegistrationComplete}
        />
      )}

      {appState === "registration-success" && (
        <RegistrationSuccess
          requiresApproval={requiresApproval}
          onContinue={handleContinueToDashboard}
        />
      )}
    </div>
  );
}