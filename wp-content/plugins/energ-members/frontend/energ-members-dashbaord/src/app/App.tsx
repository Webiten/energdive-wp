import { useEffect, useState } from "react";

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
  const [identifier, setIdentifier] = useState("");
  const [requiresApproval, setRequiresApproval] = useState(false);

  // ✅ SESSION TIMEOUT — SAFE WAY
  useSessionTimeout(
    appState === "dashboard"
      ? {
          timeoutMs: 5 * 60 * 1000,
          onExpire: () => setAppState("login"),
        }
      : null
  );

  // 🧪 Debug (confirm render)
  useEffect(() => {
    console.log("✅ App rendered, state =", appState);
  }, [appState]);

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
        <div className="size-full bg-gray-50">
          <TopBar onLogout={() => setAppState("login")} />
          <SecondHeader
            activeSection={activeSection}
            onSectionChange={setActiveSection}
          />
          {renderDashboardContent()}
        </div>
      ) : (
        <div className="size-full">
          {appState === "login" && (
            <LoginPage onVerificationSent={(v) => {
              setIdentifier(v);
              setAppState("verification");
            }} />
          )}

          {appState === "verification" && (
            <VerificationPage
              identifier={identifier}
              onVerified={(isNew) =>
                setAppState(isNew ? "register" : "dashboard")
              }
              onResend={() => AuthAPI.requestOtp(identifier)}
            />
          )}

          {appState === "register" && (
            <RegisterPage
              identifier={identifier}
              onRegistrationComplete={() => setAppState("dashboard")}
            />
          )}

          {appState === "registration-success" && (
            <RegistrationSuccess
              requiresApproval={requiresApproval}
              onContinue={() => setAppState("dashboard")}
            />
          )}
        </div>
      )}
    </>
  );
}
