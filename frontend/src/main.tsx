import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { BrowserRouter } from 'react-router-dom'
import { Toaster } from 'react-hot-toast'
import './index.css'
import App from './App.tsx'
import { AuthProvider } from './app/auth'
import { ReadOnlyProvider } from './app/readOnly'

const queryClient = new QueryClient()

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <ReadOnlyProvider>
          <BrowserRouter>
            <App />
            <Toaster position="top-center" />
          </BrowserRouter>
        </ReadOnlyProvider>
      </AuthProvider>
    </QueryClientProvider>
  </StrictMode>,
)
