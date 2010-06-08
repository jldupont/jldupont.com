/**
 * @file   list.c
 *
 * @date   2009-05-22
 * @author Jean-Lou Dupont
 */

#include "cjld.h"
#include "macros.h"


cjld_list *cjld_list_create(int id, void (*cleaner)(void *el, int id)) {

	cjld_list *tmp=NULL;

	tmp = (cjld_list *) malloc( sizeof(cjld_list) );
	if (NULL==tmp)
		return NULL;

	tmp->count   = 0;
	tmp->id      = id;
	tmp->cleaner = cleaner;
	tmp->head    = NULL;
	tmp->tail    = NULL;

	return tmp;
}//


int cjld_list_getid(cjld_list *list) {

	TESTPTR(cjld_list_get_id, list);

	return list->id;

}//


int cjld_list_count(cjld_list *list) {

	TESTPTR(cjld_list_get_id, list);

	return list->count;
}//



void __cjld_cleaner_free(void *el, int id) {
	if (NULL==el) {
		DEBUG_LOG(LOG_ERR, "cjld_cleaner_free: NULL pointer" );
		return;
	}

	free(el);
}//

int cjld_list_destroy(cjld_list *list) {

	TESTPTR(cjld_list_destroy, list);

	void (*cleaner)(void *, int);
	cleaner = list->cleaner;

	if (NULL==cleaner)
		cleaner =  &__cjld_cleaner_free;

	cjld_list_visit( list, cleaner );

	free(list);
}//


int cjld_list_append( cjld_list *dest, void *el, int id ) {

	TESTPTR(cjld_list_append, dest);

	int code = 1; //optimistic

	cjld_snode *new_node;

	new_node = (cjld_snode *) malloc(sizeof(cjld_snode));

	if (NULL!=new_node) {

		// new node...
		new_node->el   = el;
		new_node->next = NULL;

		// there is a tail... put at the end
		if (NULL!=dest->tail)
			(dest->tail)->next=new_node;

		// point tail to the new element
		dest->tail = new_node;

		// adjust head
		if (NULL==dest->head)
			dest->head=new_node;

		dest->count++;

	} else {

		code = 0;
	}

	return code;
}//


cjld_list *cjld_list_extend( cjld_list *dest, cjld_list *src ) {

	//nothing to add
	if (NULL==src) {
		return dest;
	}

	//nothing to add to
	if (NULL==dest) {
		return src;
	}

	//first, link the lists
	(dest->tail)->next = src->head;

	//adjust tail of extended list
	dest->tail = src->tail;

	//adjust size
	dest->count += src->count;

	//free the list container... not the elements
	free(src);

	return dest;
}//


void *cjld_list_remove(cjld_list *list, int id) {

	TESTPTR(cjld_list_remove, list);

	void *element=NULL;
	cjld_snode	*current_node=list->head,
				*previous_node=NULL,
				*removed_node=NULL;


	while(NULL!=current_node) {

		if (id == current_node->id) {
			removed_node = current_node;
			break;
		}
		previous_node = current_node;
		current_node = current_node->next;
	};

	//case 1: only one element and it is the one to remove => adjust both head & tail
	//case 2: one element to remove from a plurality of elements
	//     a) element at the tail => use previous element to adjust tail
	//     b) element at the head
	//     c) element in between head & tail

	if (NULL==removed_node) {
		return NULL;
	}

	//case 1
	if ( (list->tail==removed_node) && (list->head==removed_node) ) {
		list->head = NULL;
		list->tail = NULL;
		list->count = 0;
		element = removed_node->el;
		free(removed_node);
		return element;
	}

	//case 2a
	if (list->tail == removed_node) {
		previous_node->next = NULL;
		list->tail = previous_node;
		list->count--;
		element = removed_node->el;
		free(removed_node);
		return element;
	}

	//case 2b
	if (list->head == removed_node) {
		list->head = (list->head)->next;
		list->count--;
		element = removed_node->el;
		free(removed_node);
		return element;
	}

	//case 2c
	// must join the two list segments
	previous_node->next = removed_node->next;
	list->count--;
	element = removed_node->el;
	free(removed_node);

	return element;
}//


/**
 * Pointers 'list' and 'el' should already have been validated
 */
void __cjld_list_destroy_element(cjld_list *list, void *el, int id) {

	void (*cleaner)(void *el, int id);
	cleaner = list->cleaner;

	if (NULL==cleaner) {
		free(el);
	}

	*cleaner(el, id);

}//



int cjld_list_remove_destroy(cjld_list *list, int id) {

	void *removed_element;

	removed_element = cjld_list_remove( list, id );

	//not found =>error
	if (NULL==removed_element) {
		return 0;
	}


	__cjld_list_destroy_element( list, removed_element, id );

	return 1;
}//



void *cjld_list_pop(cjld_list *list) {

	TESTPTRV(cjld_list_pop, list);

	void *element=NULL;
	cjld_snode  *node=NULL,
				*next=NULL;

	next = list->head->next;
	node = list->head;
	list->head = next;

	el=node->el;

	free(node);

	return element;
}//


/**
 * Case 1: HEAD insertion
 * Case 2: TAIL insertion
 * Case 3: In between
 */
int cjld_list_insert(cjld_list *list, void *el, int id, int pos) {

	TESTPTR(cjld_list_insert, list);
	TESTINDEX(cjld_list_insert, pos);
	TESTINDEXU(cjld_list_insert, pos, list->count );

	int i=0;
	cjld_snode  *current  = NULL,
				*previous = NULL,
				*node     = NULL;

	//create node
	node = (cjld_snode *) malloc( sizeof(cjld_snode *) );
	node->el = el;
	node->id = id;

	//case 1
	if (0==pos) {
		node->next = list->head;
		list->head = node;
		list->count++;
		return 1;
	}

	//case 2 & 3: need to walk the list
	// as it is single-linked
	// NOTE: tail pointer never needs to be updated
	current  = list->head;
	previous = NULL;

	//walk till pos
	while( i != pos ) {
		previous=current;
		current=current->next;
		i++;
	}//while

	//insert in-between
	previous->next = node;
	node->next = current;

	return 1;
}//


/**
 *
 * s[i:j]
 * s[
 *
 * Case 1: empty list
 * Case 2: one element
 * Case 3:
 *
 */
int *cjld_list_slice(cjld_list *list, int start, int stop) {

	TESTPTR(cjld_list_insert, list);
	TESTINDEX(cjld_list_insert, start);
	TESTINDEX(cjld_list_insert, stop);


}//



