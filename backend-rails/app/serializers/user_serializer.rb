class UserSerializer
  include JSONAPI::Serializer
  attributes :id, :email, :firstname, :lastname, :customer_id
end
