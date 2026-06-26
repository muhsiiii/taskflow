import axios from 'axios'

export function useAuth() {
    const token = localStorage.getItem('token')
    const user  = JSON.parse(localStorage.getItem('user') || 'null')

    function getHeaders() {
        return token ? { Authorization: `Bearer ${token}` } : {}
    }

    function clearAuth() {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
    }

    async function requireAuth() {
        if (!token) {
            window.location.replace('/login')
            return false
        }

        try {
            await axios.get('/api/auth/me', { headers: getHeaders() })
            return true
        } catch (e) {
            clearAuth()
            window.location.replace('/login')
            return false
        }
    }

    async function logout() {
        try {
            await axios.post('/api/auth/logout', {}, { headers: getHeaders() })
        } catch (e) {}
        finally {
            clearAuth()
            window.location.replace('/login')
        }
    }

    return { token, user, getHeaders, requireAuth, logout }
}