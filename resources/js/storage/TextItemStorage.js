export default {
    fetchAvailableItems(resourceName, fieldAttribute, params) {
        return Nova.request().get(
            `/nova-vendor/text-auto-complete/${resourceName}/text-items/${fieldAttribute}`,
            params
        )
    },
}
