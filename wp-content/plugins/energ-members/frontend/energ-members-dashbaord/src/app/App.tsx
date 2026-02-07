import { useState, useEffect } from "react";

// 🔐 Auth Components
import { LoginPage } from "./components/auth/LoginPage";
import { VerificationPage } from "./components/auth/VerificationPage";
import { RegisterPage } from "./components/auth/RegisterPage";
import { RegistrationSuccess } from "./components/auth/RegistrationSuccess";
import { SessionExpiredModal } from "./components/SessionExpiredModal";

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
  | "registration-success";

export default function App() {
  const [appState, setAppState] = useState<AppState>("login");
  const [identifier, setIdentifier] = useState("");
  const [activeSection, setActiveSection] = useState("dashboard");

  const isOnDashboardPage =
    window.location.pathname.includes("/dashboard");

  const [hasToken, setHasToken] = useState<boolean>(false);

  useEffect(() => {
    const token =
      localStorage.getItem("access_token") ||
      localStorage.getItem("auth_token");

    setHasToken(!!token);
  }, []);

  /* =======================
     AUTH FLOW HANDLERS
  ======================= */

  const handleVerificationSent = (value: string) => {
    setIdentifier(value);
    setAppState("verification");
  };

  // ✅ Corrected logic (no loop)
  const handleVerified = (isNewUser: boolean) => {
    if (isNewUser) {
      setAppState("register");      // New user onboarding
    } else {
      window.location.href = "/dashboard"; // Existing user → dashboard
    }
  };

  const handleResendVerification = async () => {
    if (!identifier) return;
    await AuthAPI.requestOtp(identifier);
  };

  // 🔥 CRITICAL FIX — breaks thankyou loop
  const handleRegistrationComplete = () => {
    setAppState("registration-success");

    // Remove onboarding flag so dashboard doesn’t bounce back
    localStorage.removeItem("pending_registration");
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

      {/* ========= DASHBOARD GATE ========= */}
      {isOnDashboardPage && hasToken ? (
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
        /* ========= AUTH FLOW ========= */
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
              email={identifier}   // ✅ FIXED prop name
              onRegistrationComplete={handleRegistrationComplete}
            />
          )}

          {appState === "registration-success" && (
            <RegistrationSuccess
              requiresApproval={false}
              onContinue={() => (window.location.href = "/dashboard")} // ✅ Direct dashboard
            />
          )}
        </div>
      )}
    </>
  );
}
