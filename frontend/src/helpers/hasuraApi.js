import { print } from 'graphql'
import { useAuthStore } from 'stores/Auth'
import { fetchWrapper } from 'src/helpers/fetchWrapper'

class HasuraApi {

    constructor() {
        this.url = process.env.HASURA_API
        this.auth = useAuthStore()
    }

    async graphql(query, variables) {

        if(this.auth.isTokenExpired()) {
            console.log("Try to refresh token...")
            await this.auth.refresh()
        }

        query = print(query)

        const data = await fetchWrapper.post(this.url, {
            query: query, variables: variables
            });

        console.log(data)

        return data;

    }

}

export default HasuraApi