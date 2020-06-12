export default {
    fetchAvailableItems(resourceName, resourceId, fieldAttribute, params) {
        return Nova.request().get(
            `/nova-vendor/text-auto-complete/${resourceName}/${resourceId}/text-items/${fieldAttribute}`,
            params
        )
    },
}
