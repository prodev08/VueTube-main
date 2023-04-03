export default {
	userId(state){
		return state.userId;
	},
	userIsAuthenticated(state){
		return state.loggedIn;
	},
	userIsAdmin(state){
		return state.admin;
	},
	getGoogleUser(state){
		return state.googleUser;
	}
}