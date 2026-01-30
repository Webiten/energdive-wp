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
  | "register"
  | "registration-success"
  | "dashboard";

export default function App() {
  const [appState, setAppState] = useState<AppState>("login");
  const [activeSection, setActiveSection] = useState("dashboard");

  // 🔑 Single source of truth
  const [identifier, setIdentifier] = useState("");
  const [requiresApproval, setRequiresApproval] = useState(false);

  /* =======================
     🔥 SESSION TIMEOUT (SAFE)
  ======================= */

  const sessionConfigRef = useRef<{
    timeoutMs: number;
    onExpire: () => void;
  } | null>(null);

  useEffect(() => {
    if (appState === "dashboard") {
      sessionConfigRef.current = {
        timeoutMs: 5 * 60 * 1000, // 5 min
        onExpire: () => {
          setAppState("login");
        },
      };
    } else {
      sessionConfigRef.current = null;
    }
  }, [appState]);

  useSessionTimeout(sessionConfigRef.current);

  /* =======================
     AUTH FLOW HANDLERS
  ======================= */

  const handleVerificationSent = (value: string) => {
    setIdentifier(value);
    setAppState("verification");
  };

  const handleVerified = (isNewUser: boolean) => {
    setAppState(isNewUser ? "register" : "dashboard");
  };

  const handleResendVerification = async () => {
    if (!identifier) return;
    await AuthAPI.requestOtp(identifier);
  };

  const handleRegistrationComplete = () => {
    setRequiresApproval(false);
    setAppState("dashboard");
  };

  const handleContinueToDashboard = () => {
    setAppState(requiresApproval ? "login" : "dashboard");
  };

  /* =======================
     DASHBOARD CONTENT
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

  return (
    <>
      <SessionExpiredModal />

      {appState === "dashboard" ? (
        <div className="min-h-screen w-full bg-white">
          <TopBar onLogout={() => setAppState("login")} />

          <SecondHeader
            activeSection={activeSection}
            onSectionChange={setActiveSection}
          />

          {/* 🛡️ DASHBOARD ERROR BOUNDARY */}
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

          {appState === "registration-success" && (
            <RegistrationSuccess
              requiresApproval={requiresApproval}
              onContinue={handleContinueToDashboard}
            />
          )}
        </div>
      )}
    </>
  );
}
