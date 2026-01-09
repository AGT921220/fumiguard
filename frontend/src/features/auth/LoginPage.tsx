import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../../app/auth'
import { showError } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'

export function LoginPage() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [email, setEmail] = useState('admin@demo.test')
  const [password, setPassword] = useState('password')
  const [loading, setLoading] = useState(false)

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault()
    setLoading(true)
    try {
      await login(email, password)
      navigate('/dashboard', { replace: true })
    } catch (err) {
      showError(err, 'No se pudo iniciar sesión')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-full bg-slate-50 px-4 py-10">
      <div className="mx-auto w-full max-w-md">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900">Ingresar</h1>
        <p className="mt-1 text-sm text-slate-600">Accede a tu tenant.</p>

        <Card>
          <form onSubmit={onSubmit} className="mt-2 space-y-3">
            <div>
              <label className="text-xs font-semibold text-slate-700">Email</label>
              <Input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="tu@email.com"
              />
            </div>
            <div>
              <label className="text-xs font-semibold text-slate-700">Password</label>
              <Input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="********"
              />
            </div>
            <Button disabled={loading} type="submit" className="w-full">
              {loading ? 'Ingresando…' : 'Entrar'}
            </Button>
          </form>
        </Card>
      </div>
    </div>
  )
}

