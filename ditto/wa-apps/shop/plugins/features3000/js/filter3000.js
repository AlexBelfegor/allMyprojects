Filters3000 = function(container_selector, url)
{
    this.container_selector = container_selector;
    this.url = url;
    this.getSelector = function() {
        return this.container_selector;
    };
    this.getUrl = function() {
        return this.url;
    }
}

Filters3000Core = new Filters3000('', '');

