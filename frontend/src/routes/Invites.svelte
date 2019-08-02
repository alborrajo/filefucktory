<script>
import Router from 'svelte-spa-router';
import InvitesList from './Invites/InvitesList.svelte';
// Routes for the "inner router"
// Note that the path is still the absolute one!
const routes = {
    '/invites': InvitesList,
	//'/invites/:inviteID': Invite,
}
</script>

<Router {routes}/>