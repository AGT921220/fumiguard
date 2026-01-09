import { Link } from 'react-router-dom'
import { useAuth } from '../app/auth'
import { Button } from './Button'

export function TopBar() {
  const { me, logout } = useAuth()

  return (
    <div className="sticky top-0 z-20 border-b border-slate-200 bg-white/80 backdrop-blur">
      <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
        <Link to="/" className="text-sm font-bold tracking-tight">
          fumiguard
        </Link>
        <div className="flex items-center gap-3">
          {me ? (
            <div className="text-right">
              <div className="text-xs font-semibold text-slate-900">{me.name}</div>
              <div className="text-[11px] text-slate-500">
                {me.role} · tenant {me.tenant_id}
              </div>
            </div>
          ) : null}
          <Button onClick={() => logout()} className="bg-slate-100 text-slate-900">
            Salir
          </Button>
        </div>
      </div>
    </div>
  )
}

