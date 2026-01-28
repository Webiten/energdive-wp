import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './app/App'
import './styles/index.css'

const root = document.getElementById('energ-members-root')

if (root) {
  ReactDOM.createRoot(root).render(
    <React.StrictMode>
      <div id="energ-dashboard-root" className="min-h-screen bg-gray-50">
        <App />
      </div>
    </React.StrictMode>
  )
}
