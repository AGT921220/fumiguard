import React, { createContext, useContext, useMemo, useState } from 'react'
import { api } from './api'
import { clearToken, getToken, setToken } from './storage'

export type Me = {
  id: number
  name: string
  email: string
  tenant_id: number
  role: 'TENANT_ADMIN' | 'DISPATCHER' | 'TECHNICIAN' | 'CLIENT_VIEWER'
}

type AuthContextValue = {
  token: string | null
  me: Me | null
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => Promise<void>
  refreshMe: () => Promise<void>
  clearSession: () => void
}

const AuthContext = createContext<AuthContextValue | null>(null)

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [token, setTokenState] = useState<string | null>(() => getToken())
  const [me, setMe] = useState<Me | null>(null)

  const isAuthenticated = Boolean(token)

  async function refreshMe() {
    if (!token) {
      setMe(null)
      return
    }
    const data = await api<Me>('/api/v1/me')
    setMe(data)
  }

  async function login(email: string, password: string) {
    const data = await api<{ token: string }>('/api/v1/login', {
      method: 'POST',
      json: { email, password },
    })
    setToken(data.token)
    setTokenState(data.token)
    await refreshMe()
  }

  async function logout() {
    try {
      await api('/api/v1/logout', { method: 'POST' })
    } finally {
      clearSession()
    }
  }

  function clearSession() {
    clearToken()
    setTokenState(null)
    setMe(null)
  }

  const value = useMemo<AuthContextValue>(
    () => ({
      token,
      me,
      isAuthenticated,
      login,
      logout,
      refreshMe,
      clearSession,
    }),
    [token, me],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}

