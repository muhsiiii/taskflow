import axios from 'axios'

export function useAuth() {
    const token = localStorage.getItem('token')
    const user  = JSON.parse(localStorage.getItem('user') || 'null')

    function getHeaders() {
        return { Authorization: `Bearer ${token}` }
    }

    function requireAuth() {
        if (!token) {
            window.location.replace('/login')
            return false
        }
        return true
    }

    async function logout() {
        try {
            await axios.post('/api/auth/logout', {}, { headers: getHeaders() })
        } catch(e) {}
        finally {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            window.location.replace('/login')
        }
    }

    return { token, user, getHeaders, requireAuth, logout }
}