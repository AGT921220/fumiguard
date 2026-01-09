import { useEffect } from 'react'
import { Outlet } from 'react-router-dom'
import { useAuth } from '../app/auth'
import { useReadOnly } from '../app/readOnly'
import { Banner } from '../components/Banner'
import { BottomNav } from '../components/BottomNav'
import { TopBar } from '../components/TopBar'

export function AppLayout() {
  const { me, refreshMe, clearSession } = useAuth()
  const { isReadOnly } = useReadOnly()

  useEffect(() => {
    refreshMe().catch(() => {
      // Token inválido/expirado
      clearSession()
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  return (
    <div className="min-h-full bg-slate-50 text-slate-900">
      <TopBar />
      <div className="mx-auto max-w-5xl px-4 pt-4">
        {isReadOnly ? (
          <Banner>
            Suscripción inactiva. El sistema está en <strong>solo lectura</strong>.
          </Banner>
        ) : null}
        {me?.role === 'CLIENT_VIEWER' ? (
          <div className="mt-3 text-xs text-slate-500">
            Rol CLIENT_VIEWER: solo lectura.
          </div>
        ) : null}
      </div>
      <Outlet />
      <BottomNav />
    </div>
  )
}

