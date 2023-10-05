import { useAuthStore } from 'stores/Auth';

export const fetchWrapper = {
    get: request('GET'),
    post: request('POST'),
    put: request('PUT'),
    delete: request('DELETE')
};

function request(method) {
    return (url, body, variables) => {
        const requestOptions = {
            method,
            headers: authHeader(url),
            credentials: "include"
        };
        if (body) {
            requestOptions.headers['Content-Type'] = 'application/json';
            requestOptions.body = JSON.stringify(body);
            if (variables) {
                requestOptions.variables = variables
            }
        }
        return fetch(url, requestOptions).then(handleResponse);
    }
}

// helper functionn
function authHeader(url) {
    // return auth header with jwt if user is logged in and request is to the api url

    const { user } = useAuthStore();
    const isLoggedIn = !!user?.token;
    const isApiUrl = url.startsWith(process.env.HASURA_API);

    if (isLoggedIn && isApiUrl) {
        return { 
            // Authorization: `Bearer ${user.token}` 
        };
    } else {
        return {};
    }
}


async function handleResponse(response) {

    console.log(response);

    const isJson = response.headers?.get('content-type')?.includes('application/json');
    const data = isJson ? await response.json() : null;

    // check for error response
    if (!response.ok) {

        const { user, logout } = useAuthStore();
        if ([401, 403].includes(response.status) && user) {
            // auto logout if 401 Unauthorized or 403 Forbidden response returned from api
            logout();
        }

        // get error message from body or default to response status
        const error = (data && data.message) || response.status;
        return Promise.reject(error);
    }

    return data;
}