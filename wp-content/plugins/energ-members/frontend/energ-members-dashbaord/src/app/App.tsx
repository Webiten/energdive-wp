import { useEffect, useRef, useState } from "react";

// 🔐 Auth Components
import { LoginPage } from "./components/auth/LoginPage";
import { VerificationPage } from "./components/auth/VerificationPage";
import { RegisterPage } from "./components/auth/RegisterPage";
import { RegistrationSuccess } from "./components/auth/RegistrationSuccess";
import { SessionExpiredModal } from "./components/SessionExpiredModal";

// ⏱ Session Hook
import { useSessionTimeout } from "./hooks/useSessionTimeout";

// 📊 Dashboard Components
import { TopBar } from "./components/TopBar";
import { SecondHeader } from "./components/SecondHeader";
import { DashboardHome } from "./components/DashboardHome";
import { IntelligenceSection } from "./components/IntelligenceSection";
import { CommunitySection } from "./components/CommunitySection";
import { SubscriptionsSection } from "./components/SubscriptionsSection";
import { EventsSection } from "./components/EventsSection";
import { BookmarksSection } from "./components/BookmarksSection";
import { AccountSettingsSection } from "./components/AccountSettingsSection";

// 🛡️ Error Boundary
import { ErrorBoundary } from "./components/ErrorBoundary";

// 🌐 API
import { AuthAPI } from "./lib/api";

type AppState =
  | "login"
  | "verification"
  | "register";

export default function App() {
  const [appState, setAppState] = useState<AppState>("login");
  const [identifier, setIdentifier] = useState("");
  const [activeSection, setActiveSection] = useState("dashboard");

  /* ======================================================
     ❌ IMPORTANT CHANGE: remove "dashboard" from appState
     Dashboard will ONLY render when user manually visits /dashboard
  ====================================================== */

  /* =======================
     AUTH FLOW HANDLERS
  ======================= */

  const handleVerificationSent = (value: string) => {
    setIdentifier(value);
    setAppState("verification");
  };

  // 🔥 KEY FIX: redirect decision based on backend flag
  const handleVerified = (isNewUser: boolean) => {
    if (isNewUser) {
      window.location.href = "/thank-you";   // NEW USER
    } else {
      window.location.href = "/";            // EXISTING USER → HOME
    }
  };

  const handleResendVerification = async () => {
    if (!identifier) return;
    await AuthAPI.requestOtp(identifier);
  };

  const handleRegistrationComplete = () => {
    window.location.href = "/thank-you";
  };

  /* =======================
     DASHBOARD (only renders if user is on /dashboard page)
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

  const isOnDashboardPage =
    window.location.pathname.includes("/dashboard");

  return (
    <>
      <SessionExpiredModal />

      {isOnDashboardPage ? (
        <div className="min-h-screen w-full bg-white">
          <TopBar onLogout={() => (window.location.href = "/")} />

          <SecondHeader
            activeSection={activeSection}
            onSectionChange={setActiveSection}
          />

          <ErrorBoundary>
            {renderDashboardContent()}
          </ErrorBoundary>
        </div>
      ) : (
        <div className="min-h-screen w-full">
          {appState === "login" && (
            <LoginPage onVerificationSent={handleVerificationSent} />
          )}

          {appState === "verification" && (
            <VerificationPage
              identifier={identifier}
              onVerified={handleVerified}
              onResend={handleResendVerification}
            />
          )}

          {appState === "register" && (
            <RegisterPage
              identifier={identifier}
              onRegistrationComplete={handleRegistrationComplete}
            />
          )}
        </div>
      )}
    </>
  );
}
